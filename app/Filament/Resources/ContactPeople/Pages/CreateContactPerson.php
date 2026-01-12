<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople\Pages;

use App\Filament\Resources\ContactPeople\ContactPersonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactPerson extends CreateRecord
{
    protected static string $resource = ContactPersonResource::class;
}
