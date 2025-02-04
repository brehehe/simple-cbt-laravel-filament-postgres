<?php

namespace App\Filament\Resources\Master;

use App\Filament\Resources\Master\StudentResource\Pages;
use App\Filament\Resources\Master\StudentResource\RelationManagers;
use App\Models\Auth\User;
use App\Models\ExamStudent\ExamStudent;
use App\Models\Master\Student;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Data Siswa';

    protected static ?string $modelLabel = 'Data Siswa';

    protected static ?string $slug = 'master/siswa';

    protected static ?int $navigationSort = 33;

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
            ->columns(12)
            ->schema([
                //
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->columnSpan(12)
                    ->placeholder('Masukkan Nama'),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->columnSpan(12)
                    ->placeholder('Masukkan Username')
                    ->rules(['regex:/^\S*$/']) // Validasi untuk tidak mengandung spasi
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('username', preg_replace('/\s+/', '', $state)); // Hapus spasi otomatis
                    }),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->nullable()
                    ->numeric()
                    ->columnSpan(12)
                    ->placeholder('Masukkan Nomor Telepon'),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpan(12)
                    ->placeholder('Masukkan Email'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->columnSpan(12)
                    ->placeholder('Masukkan Password'),

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
                ->width('10px')
                ->alignCenter(),
                TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),
                TextColumn::make('username')
                ->label('Username')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->action(function($record, array $data) {
                    Static::editUser($record, $data);  // Atau langsung kirim $record ke editUser
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->role(['guru']);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStudents::route('/'),
        ];
    }

    public static function editUser($record, $data) {
        try {
            DB::beginTransaction(); // Memulai transaksi database

            $user = User::updateOrCreate([
                'id'=>$record->id
            ],[
                'name'=>$data['name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'username'=>$data['username'],
                'password'=>$data['password'] ? Hash::make($data['password']) : $record->password,
            ]);

            $user->syncRoles('siswa');

            DB::commit(); // Komit transaksi jika semua berjalan lancar
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error

            // Log error untuk debugging
            Log::error('Gagal Merubah Siswa: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Opsional: Anda bisa melempar ulang exception atau memberikan feedback ke pengguna
            throw $e;

            return Notification::make()
            ->title('Gagal!')
            ->body('Gagal Merubah Siswa!')
            ->danger()
            ->send();
        }

        Notification::make()
        ->title('Berhasil!')
        ->body('Siswa Berhasil Dirubah!')
        ->success()
        ->send();

        return;
    }
}
