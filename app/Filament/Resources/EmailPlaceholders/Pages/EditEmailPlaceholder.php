<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailPlaceholders\Pages;

use App\Filament\Resources\EmailPlaceholders\EmailPlaceholderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailPlaceholder extends EditRecord
{
    protected static string $resource = EmailPlaceholderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
