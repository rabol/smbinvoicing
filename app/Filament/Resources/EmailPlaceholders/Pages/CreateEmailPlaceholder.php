<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailPlaceholders\Pages;

use App\Filament\Resources\EmailPlaceholders\EmailPlaceholderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailPlaceholder extends CreateRecord
{
    protected static string $resource = EmailPlaceholderResource::class;
}
