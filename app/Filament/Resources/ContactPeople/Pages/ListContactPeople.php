<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople\Pages;

use App\Filament\Resources\ContactPeople\ContactPersonResource;
use App\Filament\Traits\HasResourceDocumentation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactPeople extends ListRecords
{
    use HasResourceDocumentation;

    protected static string $resource = ContactPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getHelpAction(),
            CreateAction::make(),
        ];
    }
}
