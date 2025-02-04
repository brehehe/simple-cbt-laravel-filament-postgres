<?php

namespace Database\Seeders;

use App\Models\Region\City;
use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public function __construct()
     {
         $this->table    = 'cities';
         $this->filename = base_path().'/database/seeders/csvs/cities.csv';
     }

     public function run()
     {
         DB::disableQueryLog();
         DB::table($this->table)->truncate();
         parent::run();
     }
}
