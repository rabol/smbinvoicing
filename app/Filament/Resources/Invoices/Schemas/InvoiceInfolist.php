<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer Details')
                ->description('Customer and contact information saved on the invoice.')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('customer.name')
                            ->label('Customer')
                            ->placeholder('-'),

                        TextEntry::make('customer_tax_id')
                            ->label('Tax ID')
                            ->placeholder('-'),

                        TextEntry::make('customer_email')
                            ->label('Email')
                            ->placeholder('-'),

                        TextEntry::make('customer_phone')
                            ->label('Phone')
                            ->placeholder('-'),

                        TextEntry::make('currency.name')
                            ->label('Currency')
                            ->formatStateUsing(function ($state, $record): string {
                                $symbol = InvoiceForm::currencySymbol($record?->currency_id);

                                return $state ? "{$state} ({$symbol})" : '-';
                            })
                            ->placeholder('-')
                            ->columnSpan(1),

                        TextEntry::make('paymentTerm.name')
                            ->label('Payment term')
                            ->placeholder('-'),
                    ]),
                ]),

            Section::make('Contact persons')
                ->schema([
                    RepeatableEntry::make('contactPeople')
                        ->label('')
                        ->schema([
                            Grid::make(5)->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->placeholder('-')
                                    ->columnSpan(2),

                                IconEntry::make('is_primary')
                                    ->label('Primary')
                                    ->boolean()
                                    ->columnSpan(1),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('phone')
                                    ->label('Phone')
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('position')
                                    ->label('Position / Role')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ]),
                        ])
                        ->columns(1)
                        ->contained(true)
                        ->placeholder('No contact persons.'),
                ]),

            Section::make('Invoice Information')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('number')
                            ->label('Invoice Number')
                            ->placeholder('-'),

                        TextEntry::make('issue_date')
                            ->label('Issue Date')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('due_date')
                            ->label('Due Date')
                            ->date()
                            ->placeholder('-'),
                    ]),

                    Grid::make(3)->schema([
                        TextEntry::make('bill_to_line_1')
                            ->label('Bill to (Line 1)')
                            ->placeholder('-'),

                        TextEntry::make('bill_to_line_2')
                            ->label('Bill to (Line 2)')
                            ->placeholder('-'),

                        TextEntry::make('bill_to_postal_code')
                            ->label('Postal Code')
                            ->placeholder('-'),

                        TextEntry::make('bill_to_city')
                            ->label('City')
                            ->placeholder('-'),

                        TextEntry::make('bill_to_state')
                            ->label('State')
                            ->placeholder('-'),

                        TextEntry::make('bill_to_country')
                            ->label('Country')
                            ->placeholder('-'),
                    ]),
                ]),

            Section::make('Totals')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('subtotal_amount')
                            ->label('Subtotal')
                            ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->currency_id))
                            ->placeholder('-'),

                        TextEntry::make('tax_total_amount')
                            ->label('Tax Total')
                            ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->currency_id))
                            ->placeholder('-'),

                        TextEntry::make('total_amount')
                            ->label('Total')
                            ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->currency_id))
                            ->placeholder('-'),
                    ]),
                ]),

            Section::make('Invoice Items')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            Grid::make(6)->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->placeholder('-')
                                    ->columnSpan(2),

                                TextEntry::make('description')
                                    ->label('Description')
                                    ->placeholder('-')
                                    ->columnSpan(2),

                                TextEntry::make('unit')
                                    ->label('Unit')
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('unit_price_amount')
                                    ->label('Unit Price')
                                    ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->invoice?->currency_id ?? $record?->currency_id))
                                    ->placeholder('-')
                                    ->columnSpan(1),
                            ]),

                            Grid::make(6)->schema([
                                TextEntry::make('quantity')
                                    ->label('Quantity')
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('discount_amount')
                                    ->label('Discount')
                                    ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->invoice?->currency_id ?? $record?->currency_id))
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('tax_rate')
                                    ->label('Tax Rate (%)')
                                    ->formatStateUsing(fn ($state) => $state === null ? '-' : rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.').'%')
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('line_subtotal_amount')
                                    ->label('Line Subtotal')
                                    ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->invoice?->currency_id ?? $record?->currency_id))
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('tax_amount')
                                    ->label('Tax')
                                    ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->invoice?->currency_id ?? $record?->currency_id))
                                    ->placeholder('-')
                                    ->columnSpan(1),

                                TextEntry::make('line_total_amount')
                                    ->label('Line Total')
                                    ->formatStateUsing(fn ($state, $record) => InvoiceForm::formatMoney($state, $record?->invoice?->currency_id ?? $record?->currency_id))
                                    ->placeholder('-')
                                    ->columnSpan(1),
                            ]),
                        ])
                        ->columns(1)
                        ->contained(true)
                        ->placeholder('No items.'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
