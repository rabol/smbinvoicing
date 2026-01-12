<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople\Schemas;

use Filament\Schemas\Schema;

class ContactPersonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
