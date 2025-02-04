<?php

namespace Database\Seeders;

use App\Models\SystemSetting\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas['CBT'] = [
            'name'         => 'CBT',
            'legal_name'   => 'CV. CBT',
            'website'      => 'https://cbt.com',
            'phone'        => 82124777891,
            'fax'          => null,
            'email'        => 'info@cbt.com',
            'full_address' => 'Jl. Nginden Semolo No.42 Blok B-23, Nginden Jangkungan, Kec. Sukolilo, Surabaya, Jawa Timur 60118'
        ];

        SystemSetting::create($datas[config('app.name')]);
    }
}
