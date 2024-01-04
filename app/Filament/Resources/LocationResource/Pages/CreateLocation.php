<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\Models\Location;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;

    protected function handleRecordCreation(array $data): Location
    {
        $data["business_id"] = auth()->user()->business_id;
        $data["lat"] = $data["location"]["lat"];
        $data["lng"] = $data["location"]["lng"];
        $location = Location::create($data);
        return $location;
    }
}
