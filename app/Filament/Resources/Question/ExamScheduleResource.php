<?php

namespace App\Filament\Resources\Question;

use App\Filament\Resources\Question\ExamScheduleResource\Pages;
use App\Filament\Resources\Question\ExamScheduleResource\RelationManagers;
use App\Models\ExamSchedule\ExamSchedule;
use App\Models\ExamStudent\ExamStudent;
use App\Models\Question\Question;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExamScheduleResource extends Resource
{
    protected static ?string $model = ExamSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Data Jadwal';

    protected static ?string $modelLabel = 'Data Jadwal';

    protected static ?string $slug = 'soal/jadwal';

    protected static ?int $navigationSort = 23;

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
                Select::make('question_id')
                ->label('Modul')
                ->options(Question::select('name','id')->orderBy('order','asc')->get()->pluck('name','id')->toArray())
                ->required()
                ->searchable()
                ->columnSpan(12)
                ->placeholder('Pilih Modul'),
                DateTimePicker::make('start_at')
                ->label('Mulai Tanggal')
                ->required()
                ->placeholder('Pilih Mulai Tanggal')
                ->columnSpan(12)
                ->seconds(false),
                DateTimePicker::make('end_at')
                ->label('Akhir Tanggal')
                ->required()
                ->placeholder('Pilih Akhir Tanggal')
                ->columnSpan(12)
                ->seconds(false),
                Textarea::make('description')
                ->label('Deskripsi')
                ->placeholder('Masukan Deskripsi')
                ->autosize()
                ->columnSpan(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(null)
            ->recordUrl(null)
            ->columns([
                //
                TextColumn::make('index')
                ->label('No.')
                ->rowIndex()
                ->alignCenter()
                ->width('10px'),
                TextColumn::make('question.name')
                ->label('Modul')
                ->searchable()
                ->sortable(),
                TextColumn::make('start_at')
                ->label('Mulai Tanggal')
                ->searchable()
                ->sortable(),
                TextColumn::make('end_at')
                ->label('Akhir Tanggal')
                ->searchable()
                ->sortable(),
                TextColumn::make('token')
                ->label('Token')
                ->formatStateUsing(fn ($state) => $state ? $state : '-')
                ->searchable()
                ->copyable()
                ->copyMessage('Token Berhasil Di Copy')
                ->copyMessageDuration(1500)
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('token')
                ->label('Token')
                ->color('success')
                ->icon('fas-square-binary')
                ->requiresConfirmation() // Aktifkan konfirmasi
                ->modalHeading('Request Token') // Heading modal konfirmasi
                ->modalDescription('Are you sure you want to request the token? This action is irreversible and will trigger token creation.')
                ->modalSubmitActionLabel('Yes, request token') // Label untuk tombol submit
                ->action(function (ExamSchedule $record) {
                    // Logic untuk memproses permintaan token
                    // Misalnya, melakukan update atau membuat token
                    $record->token = Str::random(10);
                    $record->save();

                    Notification::make()
                    ->title('Berhasil!')
                    ->body('Berhasil Membuat Token!')
                    ->success()
                    ->send();

                })
                ->visible(function (ExamSchedule $record) {
                    // Cek apakah start_at dan end_at ada, dan apakah waktu sekarang ada di antara keduanya
                    $now = now(); // Ambil waktu sekarang
                    return $record->start_at && $record->end_at &&
                           $now->between($record->start_at, $record->end_at) &&
                           !$record->token; // Pastikan token belum terbuat
                }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('checkMark')
                ->label('Lihat Nilai')
                ->url(function (ExamSchedule $record) {
                    $url = "/soal/jadwal/$record->id/edit";

                    return $url;
                }),
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
            'index' => Pages\ManageExamSchedules::route('/'),
            'edit' => Pages\ExamStudent\ExamStudentMark::route('/{record}/edit'),
        ];
    }
}
