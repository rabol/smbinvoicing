<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailPlaceholders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailPlaceholderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('key')
                                    ->label('Placeholder Key')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('{placeholder_name}')
                                    ->helperText('Use curly braces, e.g., {customer_name}')
                                    ->unique(ignoreRecord: true),

                                TextInput::make('label')
                                    ->label('Display Label')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Customer Name'),

                                Select::make('category')
                                    ->label('Category')
                                    ->required()
                                    ->options([
                                        'customer' => 'Customer',
                                        'invoice' => 'Invoice',
                                        'payment' => 'Payment',
                                        'company' => 'Company',
                                        'general' => 'General',
                                    ])
                                    ->searchable(),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Brief description of what this placeholder represents'),
                    ]),

                Section::make('Resolver Configuration')
                    ->description('Configure how this placeholder resolves its value')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('resolver_class')
                                    ->label('Resolver Class')
                                    ->placeholder(\App\Services\PlaceholderResolver::class)
                                    ->helperText('Full class name of the resolver'),

                                TextInput::make('resolver_method')
                                    ->label('Resolver Method')
                                    ->placeholder('resolveInvoicePlaceholder')
                                    ->helperText('Method name on the resolver class'),
                            ]),
                    ]),

                Section::make('Applicability')
                    ->description('Define where this placeholder can be used')
                    ->schema([
                        Select::make('applicable_to')
                            ->label('Applicable To')
                            ->multiple()
                            ->options([
                                'invoice_email' => 'Invoice Email',
                                'quote_email' => 'Quote Email',
                                'receipt_email' => 'Receipt Email',
                                'reminder_email' => 'Reminder Email',
                                'general_email' => 'General Email',
                            ])
                            ->helperText('Leave empty to make available everywhere'),
                    ]),
            ]);
    }
}
