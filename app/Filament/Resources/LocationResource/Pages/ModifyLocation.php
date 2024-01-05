<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Resources\Pages\Page;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
class ModifyLocation extends Page
{
    protected static string $resource = LocationResource::class;
    protected static string $view = 'filament.resources.location-resource.pages.modify-location';

    use InteractsWithRecord;
    public function mount(): void
    {
        static::authorizeResourceAccess();
    }
}
