<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailPlaceholders\Pages;

use App\Filament\Resources\EmailPlaceholders\EmailPlaceholderResource;
use App\Filament\Traits\HasResourceDocumentation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmailPlaceholders extends ListRecords
{
    use HasResourceDocumentation;

    protected static string $resource = EmailPlaceholderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getHelpAction(),
            CreateAction::make(),
        ];
    }
}
