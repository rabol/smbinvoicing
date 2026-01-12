<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;

class PlaceholderResolver
{
    public function resolveInvoicePlaceholder(Invoice $invoice, string $key): string
    {
        $customer = $invoice->customer;
        $currency = $invoice->currency;

        return match ($key) {
            '{customer_name}' => $customer->name ?? '',
            '{customer_email}' => $customer->email ?? '',
            '{customer_phone}' => $invoice->customer_phone ?? '',
            '{customer_tax_id}' => $invoice->customer_tax_id ?? '',
            '{invoice_number}' => $invoice->number ?? (string) $invoice->id,
            '{invoice_date}' => $invoice->issue_date?->format('Y-m-d') ?? '',
            '{invoice_due_date}' => $invoice->due_date?->format('Y-m-d') ?? '',
            '{invoice_subtotal}' => number_format((float) $invoice->subtotal_amount, 2),
            '{invoice_tax}' => number_format((float) $invoice->tax_total_amount, 2),
            '{invoice_total}' => number_format((float) $invoice->total_amount, 2),
            '{currency_symbol}' => $currency?->symbol ?? '$',
            '{payment_term}' => $invoice->paymentTerm?->name ?? '',
            default => '',
        };
    }
}
