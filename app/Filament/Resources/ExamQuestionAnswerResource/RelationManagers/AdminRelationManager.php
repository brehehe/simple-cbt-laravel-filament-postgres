<?php

namespace App\Filament\Resources\ExamQuestionAnswerResource\RelationManagers;

use App\Models\Question\QuestionItem;
use App\Models\Question\QuestionItemAnswer;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;

    protected static string $relationship = 'questionItemAnswers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('admin')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        $record = QuestionItem::find($this->getOwnerRecord()->getKey());

        return $table
            ->columns([
                //
                TextColumn::make('index')
                ->rowIndex()
                ->label('No.')
                ->alignCenter()
                ->width('10px'),
                TextColumn::make('alphabet')
                ->label('Alphabet')
                ->searchable()
                ->sortable(),
                TextColumn::make('title')
                ->label('Jawaban')
                ->searchable()
                ->sortable(),
                TextColumn::make('questionItemAnswer.title')
                ->label('Jawaban')
                ->formatStateUsing(fn($state) => $state ? 'Benar' : 'Salah')
                ->searchable()
                ->sortable(),

                // ToggleColumn::make('is_correct')
                // ->label('Apakah Benar?')
                // ->disabled(function ($record) {
                //     return $record->is_correct ? false : ($record->questionItemAnswers?->where('is_correct',1)?->first()?->is_correct ? true : false);
                // })

            ])
            ->filters([
                //
            ])
            ->actions([
                //
                EditAction::make()
                ->modalHeading('Ubah Jawaban')
                ->form([
                    Hidden::make('id'),
                    TextInput::make('title')
                    ->label('Jawaban')
                    ->placeholder('Masukkan jawaban')
                    ->required()
                    ->maxLength(255),

                    FileUpload::make('image')
                        ->label('Gambar')
                        ->openable()
                        ->downloadable()
                        ->image()
                        ->directory('uploads/image_details')
                        ->optimize('webp')
                        ->resize(50), // If you want to make it optional

                        // TextInput::make('alphabet')
                        // ->label('Abjad')
                        // ->placeholder('Masukkan abjad')
                        // ->required()
                        // ->maxLength(1),

                        // Toggle::make('is_correct')
                        // ->label('Apakah Benar?')
                        // ->default(false)
                        // $record->questionItemAnswers?->where('is_correct',1)?->first()?->is_correct ? true : false
                ])
                ->action(function (array $data) use ($record) {
                    DB::beginTransaction();

                    try {
                        // $alphabet = 'A'; // Default alphabet pertama

                        // // Cari huruf terakhir yang digunakan pada question_id dan question_item_id yang sama
                        // $lastAnswer = QuestionItemAnswer::where('question_id', $record->question_id)
                        //                                 ->where('question_item_id', $record->id)
                        //                                 ->orderBy('alphabet', 'desc') // Urutkan berdasarkan alphabet terbalik
                        //                                 ->first();

                        // // Jika ada jawaban sebelumnya, ambil alphabet terakhir dan increment
                        // if ($lastAnswer) {
                        //     $lastAlphabet = $lastAnswer->alphabet;
                        //     $alphabet = chr(ord($lastAlphabet) + 1); // Increment alphabet
                        // }

                        // Menyimpan data jawaban ke database
                        $jawaban = QuestionItemAnswer::updateOrCreate([
                            'question_id' => $record->question_id,
                            'question_item_id' => $record->id,
                            'id' => $data['id'],
                        ],[
                            'title' => $data['title'],
                            // 'alphabet' => $alphabet,
                            // 'is_correct' => $data['is_correct'],
                        ]);

                        // Menyimpan gambar jika ada
                        if (isset($data['image'])) {
                            // Mengambil path gambar yang sudah disimpan
                            $imagePath = $data['image'];

                            // Menyimpan path gambar ke database
                            $jawaban->update([
                                'image' => $imagePath
                            ]);
                        }

                        $question = $record;
                        $question->save();

                        // Commit transaksi jika semua berhasil
                        DB::commit();

                        // Menampilkan notifikasi sukses
                        Notification::make()
                            ->title('Berhasil')
                            ->success()
                            ->body('Jawaban berhasil dibuat!')
                            ->send();

                    } catch (\Exception $e) {
                        // Rollback transaksi jika terjadi error
                        DB::rollBack();

                        // Menampilkan notifikasi error
                        Notification::make()
                            ->title('Terjadi Kesalahan')
                            ->danger()
                            ->body('Terjadi kesalahan saat menyimpan data. Coba lagi!')
                            ->send();

                        // Log error jika diperlukan
                        Log::error('Error creating jawaban: ' . $e->getMessage());
                    }
                }),
                DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]
        )
        ->headerActions([
            Action::make('choiceAnswer')
            ->label('Pilih Jawaban')
            ->icon('fas-question')
            ->color('success')
            ->form([
                Select::make('question_item_answer_id')
                ->label('Pilih Jawaban')
                ->options(QuestionItemAnswer::select('id', 'alphabet', 'title')->where('question_id', $record->question_id)->where('question_item_id', $record->id)
                ->get()
                ->pluck('alphabet', 'id')
                ->mapWithKeys(function ($item, $key) {
                    return [$key => $item . '. ' . QuestionItemAnswer::find($key)->title]; // Gabungkan alphabet dan title
                })
                ->toArray())
                ->searchable()
            ])
            ->action(function (array $data) use ($record)  {
                QuestionItem::updateOrCreate([
                    'id'=>$record->id,
                ],[
                    'question_item_answer_id'=>$data['question_item_answer_id'],
                ]);

                Notification::make()
                    ->title('Berhasil')
                    ->success()
                    ->body('Jawaban berhasil Tersimpan!')
                    ->send();
            }),
            // Adding a custom header action button
            Action::make('buatJawaban')
                ->label('Buat Jawaban')
                ->form([
                    TextInput::make('title')
                    ->label('Jawaban')
                    ->placeholder('Masukkan jawaban')
                    ->required()
                    ->maxLength(255),

                    FileUpload::make('image')
                        ->label('Gambar')
                        ->openable()
                        ->downloadable()
                        ->image()
                        ->directory('uploads/image_details')
                        ->optimize('webp')
                        ->resize(50), // If you want to make it optional

                        // TextInput::make('alphabet')
                        // ->label('Abjad')
                        // ->placeholder('Masukkan abjad')
                        // ->required()
                        // ->maxLength(1),

                    // Toggle::make('is_correct')
                    //     ->label('Apakah Benar?')
                    //     ->default(false),
                ])
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->action(function (array $data) use ($record) {
                    DB::beginTransaction();

                    try {
                        // Menyimpan data jawaban ke database
                        $alphabet = 'A'; // Default alphabet pertama

                        // Cari huruf terakhir yang digunakan pada question_id dan question_item_id yang sama
                        $lastAnswer = QuestionItemAnswer::where('question_id', $record->question_id)
                                                        ->where('question_item_id', $record->id)
                                                        ->orderBy('alphabet', 'desc') // Urutkan berdasarkan alphabet terbalik
                                                        ->first();

                        // Jika ada jawaban sebelumnya, ambil alphabet terakhir dan increment
                        if ($lastAnswer) {
                            $lastAlphabet = $lastAnswer->alphabet;
                            $alphabet = chr(ord($lastAlphabet) + 1); // Increment alphabet
                        }

                        // Buat jawaban baru dengan alphabet yang sudah dihitung
                        $jawaban = QuestionItemAnswer::create([
                            'question_id' => $record->question_id,
                            'question_item_id' => $record->id,
                            'title' => $data['title'],
                            'alphabet' => $alphabet, // Gunakan alphabet yang sudah dihitung
                        ]);

                        // Menyimpan gambar jika ada
                        if (isset($data['image'])) {
                            // Mengambil path gambar yang sudah disimpan
                            $imagePath = $data['image']->store('uploads/image_details', 'public');

                            // Menyimpan path gambar ke database
                            $jawaban->update([
                                'image' => $imagePath
                            ]);
                        }

                        // Commit transaksi jika semua berhasil
                        DB::commit();

                        // Menampilkan notifikasi sukses
                        Notification::make()
                            ->title('Berhasil')
                            ->success()
                            ->body('Jawaban berhasil dibuat!')
                            ->send();

                    } catch (\Exception $e) {
                        // Rollback transaksi jika terjadi error
                        DB::rollBack();

                        // Menampilkan notifikasi error
                        Notification::make()
                            ->title('Terjadi Kesalahan')
                            ->danger()
                            ->body('Terjadi kesalahan saat menyimpan data. Coba lagi!')
                            ->send();

                        // Log error jika diperlukan
                        Log::error('Error creating jawaban: ' . $e->getMessage());
                    }
                }),
        ]);
    }
}
