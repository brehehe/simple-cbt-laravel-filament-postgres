<?php

namespace App\Filament\Resources\Master;

use App\Filament\Resources\Master\SubjectResource\Pages;
use App\Filament\Resources\Master\SubjectResource\RelationManagers;
use App\Models\ExamStudent\ExamStudent;
use App\Models\Subject\Subject;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Data Mata Pelajaran';

    protected static ?string $modelLabel = 'Data Mata Pelajaran';

    protected static ?string $slug = 'master/mata-pelajaran';

    protected static ?int $navigationSort = 34;

    protected static ?string $navigationGroup = 'Master';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasAnyRole('admin');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns('12')
            ->schema([
                //
                TextInput::make('name')
                ->label('Mata Peljaran')
                ->required()
                ->placeholder('Masukan Mata Pelajaran')
                ->columnSpan('12')
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
                ->label('Mata Pelajaran')
                ->searchable()
                ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->modalWidth('lg'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubjects::route('/'),
        ];
    }
}
