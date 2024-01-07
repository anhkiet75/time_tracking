<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    /**
     * Handle the Location "created" event.
     */
    public function created(Location $location): void
    {
        //
    }

    /**
     * Handle the Location "creating" event.
     */
    public function creating(Location $location): void
    {
        $location->business_id = auth()->user()->business_id;
    }


    /**
     * Handle the Location "creating" event.
     */
    public function updating(Location $location): void
    {
        $location->business_id = auth()->user()->business_id;
        if (isset($location->location->lat)) $location->lat = $location->location->lat;
        if (isset($location->location->lng)) $location->lng = $location->location->lng;
    }
    /**
     * Handle the Location "updated" event.
     */
    public function updated(Location $location): void
    {
        //
    }

    /**
     * Handle the Location "deleted" event.
     */
    public function deleted(Location $location): void
    {
        //
    }

    /**
     * Handle the Location "restored" event.
     */
    public function restored(Location $location): void
    {
        //
    }

    /**
     * Handle the Location "force deleted" event.
     */
    public function forceDeleted(Location $location): void
    {
        //
    }
}
