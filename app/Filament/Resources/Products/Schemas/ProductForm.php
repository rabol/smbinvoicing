<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('taxable')
                    ->required(),
                TextInput::make('unit'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
            ]);
    }
}
