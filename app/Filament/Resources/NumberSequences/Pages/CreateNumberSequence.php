<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences\Pages;

use App\Filament\Resources\NumberSequences\NumberSequenceResource;
use App\Models\NumberSequence;
use Filament\Resources\Pages\CreateRecord;

class CreateNumberSequence extends CreateRecord
{
    protected static string $resource = NumberSequenceResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        return NumberSequence::createPattern($data);

    }
}
