<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\Models\Business;
use App\Models\Location;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;
    protected ?string $subheading = "";
    protected $model = null;

    function __construct()
    {
        $business = Business::find(auth()->user()->business_id);
        $max_allow_locations = $business->max_allow_locations;
        $number_of_locations = $business->locations->count();
        $this->subheading = isset($max_allow_locations) ?  "{$number_of_locations} of {$max_allow_locations} locations is used" : "";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
