<?php

declare(strict_types=1);

namespace App\Filament\Resources\States\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('country.name')
                    ->label('Country'),
                TextEntry::make('name'),
                TextEntry::make('country_code')
                    ->placeholder('-'),
            ]);
    }
}
