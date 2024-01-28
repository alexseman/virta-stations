<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Station;
use Illuminate\Support\Facades\Cache;

class StationObserver
{
    /**
     * Handle the Station "created" event.
     */
    public function created(Station $station): void
    {
        Cache::forever(
            Station::getIndividualCacheKey($station->getKey()),
            $station->getAttributes()
        );
    }

    /**
     * Handle the Station "updated" event.
     */
    public function updated(Station $station): void
    {
        Cache::put(
            Station::getIndividualCacheKey($station->getKey()),
            $station->getAttributes()
        );
    }

    /**
     * Handle the Station "deleted" event.
     */
    public function deleted(Station $station): void
    {
        Cache::forget(
            Station::getIndividualCacheKey($station->getKey())
        );
    }
}
