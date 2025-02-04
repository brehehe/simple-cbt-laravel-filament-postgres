<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            // [
            //     'name'     => 'Super Admin',
            //     'email'    => 'superadmin@burningroom.co.id',
            //     'username' => 'superadmin',
            //     'roles'    => ['super-admin'],
            // ],
            [
                'name'     => 'Admin',
                'email'    => 'admin@cbt.com',
                'username' => 'admin',
                'roles'    => ['admin'],
            ],
            [
                'name'     => 'Guru',
                'email'    => 'guru@cbt.com',
                'username' => 'guru',
                'roles'    => ['guru'],
            ],
            [
                'name'     => 'Siswa',
                'email'    => 'siswa@cbt.com',
                'username' => 'siswa',
                'roles'    => ['siswa'],
            ],
        ];

        $password = Hash::make(12345678);

        foreach ($datas as $data) {

            $admin = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'username' => $data['username'],
                'password' => $password,
            ]);

            $admin->syncRoles($data['roles']);

        }
    }
}
