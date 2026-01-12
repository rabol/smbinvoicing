<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\Locale;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\Currency;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\Timezone;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns() // 2 is default
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(CustomerType::class)
                            ->required()
                            ->default(CustomerType::Company)   // or whatever default you want
                            ->native(false),
                        Select::make('status')
                            ->options(CustomerStatus::class)
                            ->default('active')
                            ->required(),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email(),
                        TextInput::make('phone')
                            ->tel()
                            ->telRegex('/^\+?[0-9\s\-\(\)]+$/'),
                        TextInput::make('tax_id')
                            ->rules(['vat_number', 'vat_number_format', 'vat_number_exist'])
                            ->validationMessages([
                                'vat_number' => 'The :attribute must be a valid VAT number.',
                                'vat_number_format' => 'The :attribute must be write in a valid number format {country_name}{vat_number}.',
                                'vat_number_exist' => 'VAT number :attribute not exist.',
                            ]),
                    ]),

                Section::make('Settings')
                    ->columns() // 2 is default
                    ->schema([
                        Select::make('locale_id')
                            ->label('Locale')
                            ->options(fn () => Locale::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        /*
                        Select::make('currency_id')
                            ->label('Currency')
                            ->options(fn () => Currency::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        */
                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship(
                                name: 'currency',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query): void {
                                    // Pick a single row per currency code (and optionally symbol)
                                    // This avoids duplicate labels in the dropdown.
                                    $query
                                        ->select('currencies.*')
                                        ->join(
                                            DB::raw('(SELECT MIN(id) AS id FROM currencies GROUP BY code) x'),
                                            'x.id',
                                            '=',
                                            'currencies.id'
                                        )
                                        ->orderBy('name');
                                },
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('timezone_id')
                            ->label('Timezone')
                            ->options(fn () => Timezone::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_term_id')
                            ->label('Payment term')
                            ->relationship('paymentTerm', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($record) => $record && $record->status !== 'draft'),
                        DateTimePicker::make('archived_at'),
                    ]),

                Repeater::make('addresses')
                    ->relationship('addresses')
                    ->schema([
                        Select::make('type')
                            ->options(AddressType::class)
                            ->required(),
                        TextInput::make('label'),
                        TextInput::make('address_line_1')
                            ->required(),
                        TextInput::make('address_line_2'),
                        TextInput::make('postal_code'),
                        Select::make('country_id')
                            ->label('Country')
                            ->options(fn () => Country::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('state_id')
                            ->label('State')
                            ->options(fn (callable $get) => State::query()->where('country_id', $get('country_id'))->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live(),
                        Select::make('city_id')
                            ->label('City')
                            ->options(fn (callable $get) => City::query()->where('state_id', $get('state_id'))->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload(),
                        TextInput::make('phone')
                            ->tel()
                            ->telRegex('/^\+?[0-9\s\-\(\)]+$/'),
                    ])
                    ->columns() // 2 is default
                    ->columnSpanFull(),

                // 🔽 NEW SECTION: CONTACT PERSONS
                Section::make('Contact persons')
                    ->schema([
                        Repeater::make('contactPeople')
                            ->relationship('contactPeople') // morphMany on Customer
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

                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Add contact person'),
                    ])
                    ->columnSpanFull(),
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
