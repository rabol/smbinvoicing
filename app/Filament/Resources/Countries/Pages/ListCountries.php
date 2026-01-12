<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Resources\Countries\CountryResource;
use App\Filament\Traits\HasResourceDocumentation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCountries extends ListRecords
{
    use HasResourceDocumentation;

    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getHelpAction(),
            CreateAction::make(),
        ];
    }
}
