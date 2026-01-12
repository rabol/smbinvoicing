<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type'),
                        TextEntry::make('status'),

                        TextEntry::make('name')
                            ->columnSpanFull(),

                        TextEntry::make('email')
                            ->label('Email address'),

                        TextEntry::make('phone'),

                        TextEntry::make('tax_id')
                            ->label('VAT / Tax ID'),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('locale.name')
                            ->label('Locale'),

                        TextEntry::make('currency.name')
                            ->label('Currency'),

                        TextEntry::make('timezone.name')
                            ->label('Timezone'),

                        TextEntry::make('paymentTerm.name')
                            ->label('Payment term'),

                        TextEntry::make('archived_at')
                            ->dateTime(),
                    ]),

                Section::make('Addresses')
                    ->schema([
                        RepeatableEntry::make('addresses')
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Type'),

                                TextEntry::make('label')
                                    ->label('Label'),

                                TextEntry::make('address_line_1')
                                    ->label('Address line 1')
                                    ->columnSpanFull(),

                                TextEntry::make('address_line_2')
                                    ->label('Address line 2')
                                    ->columnSpanFull(),

                                TextEntry::make('postal_code')
                                    ->label('Postal code'),

                                TextEntry::make('country.name')
                                    ->label('Country'),

                                TextEntry::make('state.name')
                                    ->label('State'),

                                TextEntry::make('city.name')
                                    ->label('City'),

                                TextEntry::make('phone')
                                    ->label('Phone'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Contact persons')
                    ->schema([
                        RepeatableEntry::make('contactPeople')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->columnSpan(1),

                                TextEntry::make('email')
                                    ->label('Email'),

                                TextEntry::make('phone')
                                    ->label('Phone'),

                                TextEntry::make('position')
                                    ->label('Position'),

                                IconEntry::make('is_primary')
                                    ->boolean()
                                    ->label('Primary'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Additional information')
                    ->schema([
                        TextEntry::make('notes')
                            ->columnSpanFull()
                            ->placeholder('No notes.'),
                    ]),
            ]);
    }
}
