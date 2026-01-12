<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople\Pages;

use App\Filament\Resources\ContactPeople\ContactPersonResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditContactPerson extends EditRecord
{
    protected static string $resource = ContactPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
