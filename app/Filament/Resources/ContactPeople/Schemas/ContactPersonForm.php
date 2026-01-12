<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople\Schemas;

use App\Models\Customer;
use App\Models\Invoice;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ContactPersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->telRegex('/^\+?[0-9\s\-\(\)]+$/')
                            ->maxLength(255),

                        TextInput::make('position')
                            ->maxLength(255),

                        Toggle::make('is_primary')
                            ->label('Primary contact'),

                        Toggle::make('add_to_invoice')
                            ->label('Add to invoice'),

                    ]),

                Section::make('Attached to')
                    ->description('Leave empty to make this a global contact.')
                    ->schema([
                        Select::make('contactable_type')
                            ->label('Type')
                            ->native(false)
                            ->options([
                                Customer::class => 'Customer',
                                Invoice::class => 'Invoice',
                            ])
                            ->searchable()
                            ->placeholder('Global contact (no attachment)')
                            ->live(),

                        Select::make('contactable_id')
                            ->label('Record')
                            ->native(false)
                            ->searchable()
                            ->options(fn (Get $get) => match ($get('contactable_type')) {
                                Customer::class => Customer::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id'),
                                Invoice::class => Invoice::query()
                                    ->orderBy('id')
                                    ->pluck('id', 'id'),
                                default => [],
                            })
                            ->visible(fn (Get $get) => filled($get('contactable_type'))),
                    ]),
            ]);
    }
}
