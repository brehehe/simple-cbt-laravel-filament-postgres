<?php

namespace App\Filament\Resources\Master\TeacherResource\Pages;

use App\Filament\Resources\Master\TeacherResource;
use App\Models\Auth\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManageTeachers extends ManageRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Guru')
            ->action(function(array $data) {
                $this->createTeacher($data);
            }),
        ];
    }

    public function createTeacher($data) {
        try {
            DB::beginTransaction(); // Memulai transaksi database

            $user = User::create([
                'name'=>$data['name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'username'=>$data['username'],
                'password'=>$data['password'] ? Hash::make($data['password']) : Hash::make(12345678),
            ]);

            $user->syncRoles('guru');

            DB::commit(); // Komit transaksi jika semua berjalan lancar
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error

            // Log error untuk debugging
            Log::error('Gagal Membuat Guru: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Opsional: Anda bisa melempar ulang exception atau memberikan feedback ke pengguna
            throw $e;

            return Notification::make()
            ->title('Gagal!')
            ->body('Gagal Membuat Guru!')
            ->danger()
            ->send();
        }

        Notification::make()
        ->title('Berhasil!')
        ->body('Guru Berhasil Dibuat!')
        ->success()
        ->send();

        return;
    }
}
