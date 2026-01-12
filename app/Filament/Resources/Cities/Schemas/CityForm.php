<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->required(),
                Select::make('state_id')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
