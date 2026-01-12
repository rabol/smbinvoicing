<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('iso2'),
                TextEntry::make('name'),
                TextEntry::make('status')
                    ->numeric(),
                TextEntry::make('phone_code'),
                TextEntry::make('iso3'),
                TextEntry::make('region'),
                TextEntry::make('subregion'),
            ]);
    }
}
