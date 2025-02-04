<?php

namespace App\Filament\Pages\ExamStudent;

use App\Models\ExamStudent\ExamStudent;
use App\Models\ExamStudent\ExamStudentDetail;
use App\Models\Question\QuestionItem;
use App\Models\Question\QuestionItemAnswer;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as ActionsAction;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ExamStudentQuestion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $view = 'filament.pages.exam-student.exam-student-question';

    protected static ?string $navigationLabel = 'Ujian';

    protected static ?string $modelLabel = 'Ujian';

    protected static ?string $title = 'Ujian';

    protected static ?string $slug = 'ujian/ujian/detail';

    protected static ?int $navigationSort = 42;

    protected static ?string $navigationGroup = 'Ujian';

    protected int|string|array $columnSpan = '4';

    public $exam_student;

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return ExamStudent::where('status', 'proses')->where('user_id', Auth::user()->id)->first() ? true : false;
    }

    // public static function canAccess(): bool
    // {
    //     return ExamStudent::where('status','proses')->where('user_id', Auth::user()->id)->first() ? true : false;
    // }

    public function mount()
    {
        $exam_student = ExamStudent::where('status', 'proses')->where('user_id', Auth::user()->id)->count();
        if (! $exam_student > 0) {
            return redirect('ujian/ujian');
        }

        if (env('APP_ENV') != 'local') {
            $examData = json_decode(Redis::get('exam_student_id'), true); // Decode as array
        } else {
            $examData = json_decode(Session::get('exam_student_id'), true); // Decode as array
        }

        // Ensure $examData is an array and contains 'id'
        $check_exam = $examData ? $examData : null;
        if (!$check_exam) {
            return redirect('ujian/ujian');
        }

        $this->exam_student = ExamStudent::find($check_exam)->id;
        if (!$this->exam_student) {
            return redirect('ujian/ujian');
        }

        $examStudentDetail = ExamStudentDetail::where('exam_student_id', $this->exam_student)
            ->first();

        $this->getExamStudentDetailFromLivewire($examStudentDetail->id);

        $this->form->fill($this->data);
    }

    protected $listeners = ['examStudentDetailUpdated' => 'getExamStudentDetailFromLivewire', 'processExam' => 'submitUjian'];

    public function getExamStudentDetailFromLivewire($id)
    {
        $examStudentDetail = ExamStudentDetail::where('exam_student_id', $this->exam_student)
            ->find($id);

        $examStudentDetails = ExamStudentDetail::select('id')->where('exam_student_id', $this->exam_student)
        ->orderBy('created_at') // Sesuaikan jika ada urutan yang lebih spesifik
        ->pluck('id')
        ->toArray();

        $nomor = array_search($id, $examStudentDetails) + 1; // +1 agar dimulai dari 1

        $this->data = [
            'exam_student_detail_id' => $examStudentDetail->id,
            'question_item_id' => $examStudentDetail->questionItem->id,
            'soal' => $examStudentDetail->questionItem->title,
            'nomor' => $nomor,
            'description' => $examStudentDetail->questionItem->description,
            'photo' => $examStudentDetail->questionItem->image ?? null,
            'question_item_answer_id' => $examStudentDetail->question_item_answer_id,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Section::make('Soal')
                    ->columnSpan(12)
                    ->description(function () {
                        return 'Soal Nomor : ' . $this->data['nomor'];
                    })
                    ->key('question_section') // Tambahkan key unik
                    ->schema([
                        Placeholder::make('photo')
                            ->label('')
                            ->content(function () {
                                // Mendapatkan URL gambar dari storage
                                $photoUrl = Storage::url($this->data['photo']);

                                // Kembalikan HTML gambar sebagai raw
                                return $this->data['photo'] ? view('livewire.components.photo', ['photoUrl' => $photoUrl]) : null;
                            })
                            ->extraAttributes(['class' => 'whitespace-nowrap'])
                            ->columnSpan(12)
                            ->visible($this->data['photo'] ? true : false),
                        Placeholder::make('soal')
                            ->label('')
                            ->content(function (): string {
                                // return $this->data['nomor'] .'. '.$this->data['soal'];
                                return $this->data['soal'];
                            })
                            ->extraAttributes(['class' => 'whitespace-nowrap'])
                            ->columnSpan(12),
                        Placeholder::make('description')
                            ->label('')
                            ->content(function (): string {
                                return $this->data['description'] ?? '-';
                            })
                            // ->extraAttributes(['class' => 'whitespace-nowrap'])
                            ->columnSpan(12)
                            ->visible($this->data['description'] ? true : false),
                        Placeholder::make('hr')
                        ->label('')
                        ->content(function () {
                            return view('livewire.components.hr');
                        })
                        ->columnSpan(12),
                        Radio::make('question_item_answer_id')
                            ->reactive()
                            ->options(function () {
                                $answers = QuestionItemAnswer::select('id','alphabet','title','image')->where('question_item_id', $this->data['question_item_id'])->get();

                                // Ambil dan proses pilihan jawaban terbaru
                                return $answers
                                    ->values() // Reset indeks agar dimulai dari 0
                                    ->mapWithKeys(function ($detail) {
                                        // Menggunakan view untuk merender gambar dan label
                                        return [
                                            $detail['id'] => view('livewire.components.photoradio', [
                                                'alphabet' => $detail['alphabet'],
                                                'title' => $detail['title'],
                                                'imageUrl' => $detail['image'] ? $detail['image'] : null, // Pastikan ini adalah URL gambar yang benar
                                            ]), // Menghasilkan HTML sebagai string
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->columns(1)
                            ->gridDirection('row')
                            ->columnSpan(12)
                            ->afterStateUpdated(function ($state) {
                                return $this->data['question_item_answer_id'] = $state;
                            })
                            ->label('Jawaban'),
                    ])->headerActions([
                    Action::make('previous')
                        ->label('Soal Sebelumnya')
                        ->color('gray')
                        ->hidden(function () {
                            $currentId = $this->data['exam_student_detail_id'];

                            return ! ExamStudentDetail::where('id', '<', $currentId)->exists();
                        })
                        ->action(function () {
                            $currentId = $this->data['exam_student_detail_id'];

                            // Cari Soal Sebelumnyanya (ID lebih kecil)
                            $previous = ExamStudentDetail::select('id')->where('id', '<', $currentId)
                                ->orderBy('id', 'desc')
                                ->first();

                            $this->updateData($previous->id);
                        }),

                    Action::make('hesitate')
                        ->label('Ragu - Ragu')
                        ->color('warning')
                        ->action(function () {
                            $question_item_answer_id = $this->data['question_item_answer_id'];

                            if (! $question_item_answer_id) {
                                return Notification::make()
                                    ->title('Pilih Jawaban')
                                    ->body('Harap Pilih Jawaban Terlebih Dahulu')
                                    ->warning()
                                    ->send();
                            }

                            $examStudentDetail = ExamStudentDetail::find($this->data['exam_student_detail_id']);
                            $examStudentDetail->question_item_answer_id = $question_item_answer_id;
                            $examStudentDetail->status = 'ragu-ragu';
                            $examStudentDetail->save();

                            $currentId = $this->data['exam_student_detail_id'];

                            // Cari soal berikutnya (ID lebih besar)
                            $next = ExamStudentDetail::select('id')->where('id', '>', $currentId)
                                ->orderBy('id', 'asc')
                                ->first();

                            $this->updateData($next->id);
                        }),

                    Action::make('choice')
                        ->label('Simpan Jawaban')
                        ->color('success')
                        ->action(function () {
                            $question_item_answer_id = $this->data['question_item_answer_id'];

                            if (! $question_item_answer_id) {
                                return Notification::make()
                                    ->title('Pilih Jawaban')
                                    ->body('Harap Pilih Jawaban Terlebih Dahulu')
                                    ->warning()
                                    ->send();
                            }

                            $examStudentDetail = ExamStudentDetail::find($this->data['exam_student_detail_id']);
                            $examStudentDetail->question_item_answer_id = $question_item_answer_id;
                            $examStudentDetail->status = 'terpilih';
                            $examStudentDetail->save();

                            $currentId = $this->data['exam_student_detail_id'];

                            // Cari soal berikutnya (ID lebih besar)
                            $next = ExamStudentDetail::select('id')->where('id', '>', $currentId)
                                ->orderBy('id', 'asc')
                                ->first();

                            $this->updateData($next->id);
                        }),

                    Action::make('next')
                        ->label('Soal Selanjutnya')
                        ->color('gray')
                        ->hidden(function () {
                            $currentId = $this->data['exam_student_detail_id'];

                            return ! ExamStudentDetail::where('id', '>', $currentId)->exists();
                        })
                        ->action(function () {
                            $currentId = $this->data['exam_student_detail_id'];

                            // Cari soal berikutnya (ID lebih besar)
                            $next = ExamStudentDetail::select('id')->where('id', '>', $currentId)
                                ->orderBy('id', 'asc')
                                ->first();

                            $this->updateData($next->id);
                        }),
                ]),
            ])->statePath('data');
    }

    public function getHeaderActions(): array
    {
        return [
            ActionsAction::make('submit')
                ->color('success')
                ->label('Selesai Ujian')
                ->requiresConfirmation()
                ->modalHeading('Selesai Ujian')
                ->modalDescription('Apakah Anda yakin ingin menyelesaikan ujian? Setelah dikonfirmasi, Anda tidak dapat mengubah jawaban.')
                ->modalSubmitActionLabel('Ya, selesaikan')
                ->action(function () {
                    $this->submitUjian();
                }),
        ];
    }

    public function submitUjian()
    {
        $exam_student = ExamStudent::find($this->exam_student);

        $total_soal = $exam_student->details->count();

        $hitung_nilai = $total_soal = 100 / $total_soal;

        $getStatusTerpilih = [];

        foreach ($exam_student->details as $key => $detail) {
            if ($detail->status == 'terpilih') {
                $questionAnswer = $detail->questionItem->question_item_answer_id;
                if ($detail->question_item_answer_id == $questionAnswer) {
                    $getStatusTerpilih[] = $detail->question_item_answer_id;
                }
            }
        }

        $total_benar = count($getStatusTerpilih);

        $exam_student->mark = $total_benar * $hitung_nilai;
        $exam_student->status = 'selesai';
        $exam_student->save();

        Notification::make()
            ->title('Berhasil')
            ->body('Ujian Selesai')
            ->success()
            ->send();

        Session::forget('exam_student_id');

        return redirect('/ujian/ujian');
    }

    public function updateData($examStudentDetailId) {
        $examStudentDetails = ExamStudentDetail::select('id')->where('exam_student_id', $this->exam_student)
        ->orderBy('created_at') // Sesuaikan jika ada urutan yang lebih spesifik
        ->pluck('id')
        ->toArray();

        $examStudentDetail = ExamStudentDetail::find($examStudentDetailId);

        $nomor = array_search($examStudentDetailId, $examStudentDetails) + 1; // +1 agar dimulai dari 1

        $this->data = [
            'exam_student_detail_id' => $examStudentDetail->id,
            'question_item_id' => $examStudentDetail->questionItem->id,
            'soal' => $examStudentDetail->questionItem->title,
            'description' => $examStudentDetail->questionItem->description,
            'photo' => $examStudentDetail->questionItem->image,
            'question_item_answer_id' => $examStudentDetail->question_item_answer_id,
            'nomor' => $nomor,
        ];

        $this->dispatch('examStudentDetailLivewireUpdated', $this->data['exam_student_detail_id']);
    }
}
