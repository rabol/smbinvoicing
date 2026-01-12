<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasLabel
{
    case None = 'none';
    case Draft = 'draft';
    case InvoiceCreated = 'invoice_created';
    case Invoiced = 'invoiced';
    case InvoiceSend = 'invoice_send';

    case PartialPaid = 'partial_paid';
    case Paid = 'paid';
    case Void = 'void';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => __('None'),
            self::Draft => __('draft'),
            self::InvoiceCreated => __('Invoice created'),
            self::Invoiced => __('invoiced'),
            self::InvoiceSend => __('general.invoice_send'),
            self::PartialPaid => __('Partial paid'),
            self::Paid => __('paid'),
            self::Void => __('void'),
        };
    }
}
