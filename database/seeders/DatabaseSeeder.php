<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('optimize:clear');

        $this->call([
            SystemSettingSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            QuestionSeeder::class,
        ]);

        Artisan::call('optimize:clear');
    }
}
