<?php

namespace App\Filament\Pages\ExamStudent;

use App\Models\ExamSchedule\ExamSchedule;
use App\Models\ExamStudent\ExamStudent as ExamStudentExamStudent;
use App\Models\ExamStudent\ExamStudentDetail;
use App\Models\Question\Question;
use App\Models\Question\QuestionItem;
use App\Models\Subject\Subject;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class ExamStudent extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $view = 'filament.pages.exam-student.exam-student';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Data Ujian';

    protected static ?string $modelLabel = 'Data Ujian';

    protected static ?string $title = 'Data Ujian';

    protected static ?string $slug = 'ujian/ujian';

    protected static ?int $navigationSort = 41;

    protected static ?string $navigationGroup = 'Ujian';

    public ?array $data = [];

    public function mount()
    {
        $this->data = [
            'subject_id' => null,
            'date' => null,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Section::make()
                    ->columnSpan(12)
                    ->columns(12)
                    ->schema([
                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->searchable()
                            ->reactive()
                            ->preload()
                            ->options(Subject::select('name', 'id')->get()->pluck('name', 'id')->toArray())
                            ->afterStateUpdated(function ($state) {
                                $this->data['subject_id'] = $state;
                            })
                            ->columnSpan(12),
                        // DateTimePicker::make('date')
                        // ->label('Tanggal dan Waktu')
                        // ->reactive()
                        // ->afterStateUpdated(function ($state) {
                        //     $this->data['date'] = $state;
                        // })
                        // ->columnSpan(6),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $auth = Auth::user();
                $checkExamSchedule = ExamStudentExamStudent::select('exam_schedule_id')->where('status', 'selesai')->where('user_id', $auth->id)->get()->pluck('exam_schedule_id');

                $query = ExamSchedule::query()->whereNotIn('id', $checkExamSchedule);

                if ($this->data['subject_id']) {
                    $question = Question::where('subject_id', $this->data['subject_id'])->get()->pluck('id');
                    $query->whereIn('question_id', $question);
                }

                return $query;
            })
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex()
                    ->alignCenter()
                    ->width('10px'),
                TextColumn::make('question.name')
                    ->searchable()
                    ->label('Soal'),
                TextColumn::make('question.minutes')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state.' Menit')
                    ->label('Soal'),
                TextColumn::make('question.subject.name')
                    ->searchable()
                    ->label('Mata Pelajaran'),
                TextColumn::make('start_at')
                    ->searchable()
                    ->label('Mulai Tanggal')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('Y-m-d H:i:s')),
                TextColumn::make('end_at')
                    ->searchable()
                    ->label('Akhir Tanggal')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('Y-m-d H:i:s')),
            ])
            ->actions([
                Action::make('question')
                    ->label('Masuk Ujian')
                    ->icon('fas-book')
                    ->hidden(function (ExamSchedule $record) {
                        $examStudent = ExamStudentExamStudent::where('user_id', Auth::user()->id)->where('exam_schedule_id', $record->id)->where('status', 'proses')->first();

                        if ($examStudent) {
                            return false;
                        }

                        return true;
                    })
                    ->url(function (ExamSchedule $record) {
                        $examStudent = ExamStudentExamStudent::where('user_id', Auth::user()->id)->where('exam_schedule_id', $record->id)->where('status', 'proses')->first();

                        if (env('APP_ENV') != 'local') {
                            Redis::set('exam_student_id', json_encode($examStudent->id));
                        } else {
                            Session::put('exam_student_id', json_encode($examStudent->id));
                        }

                        $url = '/ujian/ujian/detail';

                        return $url;
                    }),
                Action::make('startExam')
                    ->label('Mulai Ujian')
                    ->icon('fas-book')
                    ->hidden(function (ExamSchedule $record) {
                        $cekExamStudent = ExamStudentExamStudent::where('user_id', Auth::user()->id)->where('status', 'proses')->first();

                        if ($cekExamStudent) {
                            return true;
                        }

                        $examStudent = ExamStudentExamStudent::where('user_id', Auth::user()->id)->where('exam_schedule_id', $record->id)->first();

                        if ($examStudent) {
                            return true;
                        }

                        return false;
                    })
                    ->form([
                        TextInput::make('token')
                            ->placeholder('Masukan Token Anda')
                            ->label('Token')
                            ->required()
                            ->minLength(10)
                            ->maxLength(10),
                    ])
                    ->action(function (array $data) {
                        $exam_schedule = ExamSchedule::where('token', $data['token'])->first();
                        if (! $exam_schedule) {
                            return Notification::make()
                                ->title('Gagal!')
                                ->body('Token Tidak Dikenali')
                                ->warning()
                                ->send();
                        }

                        $exam_student = ExamStudentExamStudent::create([
                            'exam_schedule_id' => $exam_schedule->id,
                            'question_id' => $exam_schedule->question_id,
                            'minutes' => $exam_schedule->question->minutes,
                            'user_id' => Auth::user()->id,
                        ]);

                        $question = $exam_schedule->question;

                        $question_items = $question->is_random ? QuestionItem::where('question_id', $question->id)->inRandomOrder()->get() : QuestionItem::where('question_id', $question->id)->get();

                        foreach ($question_items as $question_item) {
                            ExamStudentDetail::create([
                                'exam_student_id' => $exam_student->id,
                                'question_item_id' => $question_item->id,
                            ]);
                        }

                        Notification::make()
                            ->title('Berhasil!')
                            ->body('Silahkan Mulai Ujian')
                            ->success()
                            ->send();

                        $examStudent = ExamStudentExamStudent::where('user_id', Auth::user()->id)->where('exam_schedule_id', $exam_schedule->id)->where('status', 'proses')->first();

                        if (env('APP_ENV') != 'local') {
                            Redis::set('exam_student_id', json_encode($examStudent->id));
                        } else {
                            Session::put('exam_student_id', json_encode($examStudent->id));
                        }

                        return redirect('/ujian/ujian/detail');
                    })
                    ->visible(function (ExamSchedule $record) {
                        // Cek apakah start_at dan end_at ada, dan apakah waktu sekarang ada di antara keduanya
                        $now = now(); // Ambil waktu sekarang

                        return $record->start_at && $record->end_at &&
                               $now->between($record->start_at, $record->end_at); // Pastikan token belum terbuat
                    }),
            ]);
    }
}
