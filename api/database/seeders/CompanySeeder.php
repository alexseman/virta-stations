<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use PhpParser\Error;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyHierarchyDepth          = 4;
        $subsidiaryCountMedian          = 3;
        $subsidiaryCountMedianVariation = 1;

        if ($subsidiaryCountMedianVariation > $subsidiaryCountMedian) {
            throw new Error('[CompanySeeder] Desired subsidiary count median variation must not be bigger than the median!');
        }

        $this->create(
            $companyHierarchyDepth,
            $subsidiaryCountMedian,
            $subsidiaryCountMedianVariation,
            []
        );

        $this->outputTreeToConsole();
    }

    private function create($depth, $median, $variation, $parents): void
    {
        if (! $depth) {
            return;
        }

        if (empty($parents)) {
            $parents = Company::factory($median)->create();
            $this->create($depth - 1, $median, $variation, $parents);
        }

        $newParents = [];

        foreach ($parents as $parent) {
            $subsidiaryCount = rand($median - $variation, $median + $variation);
            $newParents      = (Company::factory($subsidiaryCount)->set('parent_id', $parent->id)->create())->merge($newParents);
        }

        $this->create($depth - 1, $median, $variation, $newParents);
    }

    private function outputTreeToConsole(): void
    {
        echo "\nCompanies Hierarchy:\n";
        $nodes = Company::get()->toTree();

        $traverse = function ($companies, $prefix = '-') use (&$traverse) {
            foreach ($companies as $company) {
                echo PHP_EOL . $prefix . ' ' . $company->name;

                $traverse($company->children, $prefix . '-');
            }
        };

        $traverse($nodes);
        echo "\n\n";
    }
}
