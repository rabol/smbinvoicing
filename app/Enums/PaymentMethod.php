<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case Check = 'check';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case Stripe = 'stripe';
    case PayPal = 'paypal';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::BankTransfer => __('Bank Transfer'),
            self::Cash => __('Cash'),
            self::Check => __('Check'),
            self::CreditCard => __('Credit Card'),
            self::DebitCard => __('Debit Card'),
            self::Stripe => __('Stripe'),
            self::PayPal => __('PayPal'),
            self::Other => __('Other'),
        };
    }
}
