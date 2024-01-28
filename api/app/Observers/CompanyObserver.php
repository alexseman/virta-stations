<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Company;
use Illuminate\Support\Facades\Cache;

class CompanyObserver
{
    /**
     * Handle the Station "created" event.
     */
    public function created(Company $company): void
    {
        Cache::forever(
            Company::getIndividualCacheKey($company->getKey()),
            $company->getAttributes()
        );
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        Cache::put(
            Company::getIndividualCacheKey($company->getKey()),
            $company->getAttributes()
        );
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        Cache::forget(
            Company::getIndividualCacheKey($company->getKey())
        );
    }
}
