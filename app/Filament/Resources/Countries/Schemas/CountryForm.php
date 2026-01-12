<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('iso2')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('phone_code')
                    ->tel()
                    ->telRegex('/^\+?[0-9\s\-\(\)]+$/')
                    ->required(),
                TextInput::make('iso3')
                    ->required(),
                TextInput::make('region')
                    ->required(),
                TextInput::make('subregion')
                    ->required(),
            ]);
    }
}
