<?php

declare(strict_types=1);

namespace App\Filament\Resources\States\Pages;

use App\Filament\Resources\States\StateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Nnjeim\World\Models\Country;

class EditState extends EditRecord
{
    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[\Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // It does not make sense to select the country and then also have to enter the country code
        $data['country_code'] = Country::find($data['country_id'])->iso2;

        return $data;
    }
}
