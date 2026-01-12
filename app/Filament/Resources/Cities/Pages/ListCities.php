<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cities\Pages;

use App\Filament\Resources\Cities\CityResource;
use App\Filament\Traits\HasResourceDocumentation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCities extends ListRecords
{
    use HasResourceDocumentation;

    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getHelpAction(),
            CreateAction::make(),
        ];
    }
}
