<?php

namespace App\Filament\Resources\Question\QuestionResource\RelationManagers;

use App\Filament\Resources\ExamQuestionAnswerResource\RelationManagers\AdminRelationManager;
use App\Models\Question\QuestionItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;

class QuestionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'questionItems';

    protected static ?string $navigationLabel = 'Data Soal';

    protected static ?string $modelLabel = 'Data Soal';

    protected static ?string $title = 'Data Soal';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('title')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('title')
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                TextColumn::make('index')
                ->label('No.')
                ->rowIndex()
                ->alignCenter()
                ->width('10px'),
                Tables\Columns\TextColumn::make('title')
                ->label('Soal')
                ->searchable(),
                // Tables\Columns\TextColumn::make('weight')
                // ->label('Bobot'),
                TextColumn::make('questionItemAnswerCorrect.alphabet')
                ->formatStateUsing(fn ($state, QuestionItem $record) => $state . ' . '. $record->questionItemAnswerCorrect->title)
                ->label('Jawaban')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Buat Soal')
                ->form([
                    Forms\Components\TextInput::make('title')
                    ->label('Soal')
                    ->required() // If you want it to be required
                    ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->nullable(), // Make it nullable if needed

                    // Forms\Components\TextInput::make('weight')
                    //     ->label('Bobot Soal')
                    //     ->numeric() // Ensure only numeric input
                    //     ->default(0),

                    Forms\Components\FileUpload::make('image')
                        ->label('Gambar')
                        ->openable()
                        ->downloadable()
                        ->image()
                        ->directory('uploads/images')
                        ->optimize('webp')
                        ->resize(50), // If you want to make it optional
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        Forms\Components\TextInput::make('title')
                        ->label('Soal')
                        ->required() // If you want it to be required
                        ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->nullable(), // Make it nullable if needed

                        // Forms\Components\TextInput::make('weight')
                        //     ->label('Bobot Soal')
                        //     ->numeric() // Ensure only numeric input
                        //     ->default(0),

                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar')
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->directory('uploads/images')
                            ->optimize('webp')
                            ->resize(50), // If you want to make it optional
                    ]),
                    Tables\Actions\EditAction::make()
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('title')
                        ->label('Soal')
                        ->required() // If you want it to be required
                        ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->nullable(), // Make it nullable if needed

                        // Forms\Components\TextInput::make('weight')
                        //     ->label('Bobot Soal')
                        //     ->numeric() // Ensure only numeric input
                        //     ->default(0),

                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar')
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->directory('uploads/images')
                            ->optimize('webp')
                            ->resize(50), // If you want to make it optional
                    ]),
                    // Action::make('answer')
                    // ->label('Jawaban')
                    // ->recordTitleAttribute('title')
                    // ->color('success')
                    // ->modalWidth('xl')
                    // ->modalActions(fn ($action) => [$action->getModalCancelAction()->label('Batal')])
                    // ->icon('fas-question') // Perbaiki icon class, gunakan spasi.
                    // ->modalContent(fn ($record) => view('livewire.question.question-item-answer-view',['record'=>$record])), // URL dinamis berdasarkan $record

                    RelationManagerAction::make('lesson-relation-manager')
                    ->label('Jawaban')
                    ->recordTitleAttribute('title')
                    ->color('success')
                    ->icon('fas-question') // Perbaiki icon class, gunakan spasi.
                    // ->modalWidth(MaxWidth::Full)
                    ->modalActions(fn ($action) => [$action->getModalCancelAction()->label('Batal')])
                    ->relationManager(AdminRelationManager::make()),

                    Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
