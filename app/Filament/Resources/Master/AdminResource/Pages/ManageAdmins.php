<?php

namespace App\Filament\Resources\Master\AdminResource\Pages;

use App\Filament\Resources\Master\AdminResource;
use App\Models\Auth\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManageAdmins extends ManageRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Admin')
            ->action(function(array $data) {
                $this->createAdmin($data);
            }),
        ];
    }

    public function createAdmin($data) {
        try {
            DB::beginTransaction(); // Memulai transaksi database

            $user = User::create([
                'name'=>$data['name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'username'=>$data['username'],
                'password'=>$data['password'] ? Hash::make($data['password']) : Hash::make(12345678),
            ]);

            $user->syncRoles('admin');

            DB::commit(); // Komit transaksi jika semua berjalan lancar
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error

            // Log error untuk debugging
            Log::error('Gagal Membuat User: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Opsional: Anda bisa melempar ulang exception atau memberikan feedback ke pengguna
            throw $e;

            return Notification::make()
            ->title('Gagal!')
            ->body('Gagal Membuat User!')
            ->danger()
            ->send();
        }

        Notification::make()
        ->title('Berhasil!')
        ->body('User Berhasil Dibuat!')
        ->success()
        ->send();

        return;
    }
}
