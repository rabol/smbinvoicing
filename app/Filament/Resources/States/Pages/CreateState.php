<?php

declare(strict_types=1);

namespace App\Filament\Resources\States\Pages;

use App\Filament\Resources\States\StateResource;
use Filament\Resources\Pages\CreateRecord;
use Nnjeim\World\Models\Country;

class CreateState extends CreateRecord
{
    protected static string $resource = StateResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // It does not make sense to select the country and then also have to enter the country code
        $data['country_code'] = Country::find($data['country_id'])->iso2;

        return $data;
    }
}
