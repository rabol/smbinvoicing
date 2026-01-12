<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invoice_id')
                    ->relationship('invoice', 'number')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Invoice')
                    ->live()
                    ->afterStateUpdated(function ($set, ?string $state) {
                        if (! $state) {
                            return;
                        }

                        $invoice = Invoice::find($state);
                        if ($invoice) {
                            $amountDue = $invoice->getAmountDue();
                            $set('amount', $amountDue);
                        }
                    }),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix(fn ($get): string => self::getCurrencySymbol($get))
                    ->step('0.001')
                    ->maxLength(255)
                    ->helperText(fn ($get): ?string => self::getAmountHelper($get)),
                Select::make('payment_method')
                    ->options(PaymentMethod::class)
                    ->required()
                    ->label('Payment Method')
                    ->native(false),
                DatePicker::make('payment_date')
                    ->required()
                    ->default(now())
                    ->label('Payment Date')
                    ->native(false),
                TextInput::make('reference_number')
                    ->maxLength(255)
                    ->label('Reference Number'),
                TextInput::make('transaction_id')
                    ->maxLength(255)
                    ->label('Transaction ID'),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    protected static function getCurrencySymbol($get): string
    {
        $invoiceId = $get('invoice_id');
        if (! $invoiceId) {
            return '$';
        }

        $invoice = Invoice::find($invoiceId);
        if (! $invoice || ! $invoice->currency) {
            return '$';
        }

        return $invoice->currency->symbol ?? '$';
    }

    protected static function getAmountHelper($get): ?string
    {
        $invoiceId = $get('invoice_id');
        if (! $invoiceId) {
            return null;
        }

        $invoice = Invoice::find($invoiceId);
        if (! $invoice) {
            return null;
        }

        $totalAmount = (float) $invoice->total_amount;
        $totalPaid = (float) $invoice->getTotalPaid();
        $amountDue = (float) $invoice->getAmountDue();
        $symbol = $invoice->currency->symbol ?? '$';

        if ($totalPaid > 0) {
            return "Invoice Total: {$symbol}{$totalAmount} | Paid: {$symbol}{$totalPaid} | Due: {$symbol}{$amountDue}";
        }

        return "Invoice Total: {$symbol}{$totalAmount} | Amount Due: {$symbol}{$amountDue}";
    }
}
