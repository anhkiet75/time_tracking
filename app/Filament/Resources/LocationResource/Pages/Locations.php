<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Resources\Pages\Page;

class Locations extends Page
{
    protected static string $resource = LocationResource::class;

    protected static string $view = 'filament.resources.location-resource.pages.locations';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }
}
