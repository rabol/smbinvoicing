<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NumberSequenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type')
                    ->required(),
                // TextInput::make('pattern')
                //    ->required(),
                TextInput::make('prefix'),
                TextInput::make('postfix'),

                Select::make('placeholders')
                    ->multiple()
                    ->required()
                    ->options([
                        'number' => 'Number',
                        // 'number_x' => 'Number x long',
                        'day' => 'Day - leading zero',
                        'month' => 'Month - leading zero',
                        'year' => 'Year - full year',
                        'day_short' => 'Day short',
                        'month_short' => 'Month short',
                        'year_short' => 'Year short',
                    ])
                    ->helperText('How should the final number look like e.g. "INV-number-year"'),

                Select::make('delimiter')
                    ->options([
                        '-' => 'Dash',
                        ' ' => 'Space',
                    ]),
                Select::make('reset_frequency')
                    ->required()
                    ->options([
                        'yearly' => 'Yearly',
                        'monthly' => 'Monthly',
                        'daily' => 'Daily',
                        'never' => 'Never',
                    ])
                    ->default('never'),
                DatePicker::make('date')
                    ->default(now())
                    ->required(),
                TextInput::make('ordinal_number')
                    ->default(1)
                    ->minValue(1)
                    ->numeric(),
            ]);
    }
}
