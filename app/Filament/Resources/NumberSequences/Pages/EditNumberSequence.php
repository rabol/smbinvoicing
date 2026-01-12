<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences\Pages;

use App\Filament\Resources\NumberSequences\NumberSequenceResource;
use App\Models\NumberSequence;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditNumberSequence extends EditRecord
{
    protected static string $resource = NumberSequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    #[\Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return NumberSequence::createPattern($data);
    }
}
