<?php

namespace Database\Seeders;

use App\Models\Region\SubDistrict;
use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubDistrictSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public function __construct()
     {
         $this->table    = 'sub_districts';
         $this->filename = base_path().'/database/seeders/csvs/sub_districts.csv';
     }

     public function run()
     {
         DB::disableQueryLog();
         DB::table($this->table)->truncate();
         parent::run();
     }
}
