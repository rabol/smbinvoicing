<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences\Pages;

use App\Filament\Resources\NumberSequences\NumberSequenceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNumberSequence extends ViewRecord
{
    protected static string $resource = NumberSequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
