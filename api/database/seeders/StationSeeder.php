<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stationsCountMedian          = 30;
        $stationsCountMedianVariation = 10;
        $companiesIds                 = Company::get()->pluck('id')->toArray();

        foreach ($companiesIds as $companyId) {
            Station::factory(
                rand($stationsCountMedian - $stationsCountMedianVariation, $stationsCountMedian + $stationsCountMedianVariation)
            )->set('company_id', $companyId)->create();
        }
    }
}
