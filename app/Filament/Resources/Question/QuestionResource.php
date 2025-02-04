<?php

namespace App\Filament\Resources\Question;

use App\Filament\Resources\Question\QuestionResource\Pages;
use App\Filament\Resources\Question\QuestionResource\RelationManagers;
use App\Filament\Resources\Question\QuestionResource\RelationManagers\QuestionItemsRelationManager;
use App\Models\Auth\User;
use App\Models\ExamStudent\ExamStudent;
use App\Models\Question\Question;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Data Soal';

    protected static ?string $modelLabel = 'Data Soal';

    protected static ?string $slug = 'soal/soal';

    protected static ?int $navigationSort = 22;

    protected static ?string $navigationGroup = 'Soal';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasAnyRole('admin','guru');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole('admin','guru');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                //
                Section::make()
                ->columnSpan(12)
                ->columns(12)
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(4)
                    ->placeholder('Masukkan nama'),

                Forms\Components\TextInput::make('minutes')
                    ->type('number')
                    ->default(0)
                    ->numeric()
                    ->columnSpan(4)
                    ->placeholder('Masukkan jumlah menit'),

                Forms\Components\Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('subject', 'name')
                    ->columnSpan(4)
                    ->createOptionForm([
                        TextInput::make('name')
                        ->label('Nama Mata Pelajaran')
                        ->required()
                    ])
                    ->placeholder('Pilih mata pelajaran'), // Placeholder untuk select

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpan(12)
                    ->placeholder('Masukkan deskripsi'),

                Forms\Components\Toggle::make('is_random')
                    ->default(false)
                    ->label('Acak')
                    ->helperText('Tandai jika acak')
                    ->columnSpan(12),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                //
                TextColumn::make('index')
                ->label('No.')
                ->rowIndex()
                ->alignCenter()
                ->width('10px'),
                TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
                TextColumn::make('minutes')
                ->label('Menit')
                ->formatStateUsing(fn ($state) => $state .' Menit')
                ->searchable(),
                TextColumn::make('subject.name')
                ->label('Mate Pelajaran'),
                TextColumn::make('user.name')
                ->label('Pembuat')
                ->searchable()
                ->sortable(),
                // TextColumn::make('order')
                // ->label('Order')
                // ->searchable()
                // ->sortable(),
                ToggleColumn::make('is_random')
                ->label('Soal Acak')
                ->alignCenter()
                ->width('10px'),
            ])
            ->filters([
                //
                SelectFilter::make('user_id')
                ->label('Pembuat')
                ->options(User::role(['admin','guru'])->get()->pluck('name','id')->toArray())
                ->multiple()
                ->searchable(),
                // Filter::make('is_random')
                // ->label('Soal Acak')
                // ->toggle()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                if (Auth::user() && Auth::user()->hasAnyRole(['guru'])) {
                    $query->where('user_id',Auth::user()->id);
                }

                // $query->orderBy('order','desc');

                return $query;
            });
    }

    public static function getRelations(): array
    {
        return [
            //
            QuestionItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            // 'create' => Pages\CreateQuestion::route('/create'),
            // 'view' => Pages\ViewQuestion::route('/{record}'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
