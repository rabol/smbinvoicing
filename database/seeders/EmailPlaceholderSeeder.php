<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailPlaceholder;
use Illuminate\Database\Seeder;

class EmailPlaceholderSeeder extends Seeder
{
    public function run(): void
    {
        $placeholders = [
            [
                'key' => '{customer_name}',
                'label' => 'Customer Name',
                'category' => 'customer',
                'description' => 'The name of the customer',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'key' => '{customer_email}',
                'label' => 'Customer Email',
                'category' => 'customer',
                'description' => 'The email address of the customer',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'key' => '{customer_phone}',
                'label' => 'Customer Phone',
                'category' => 'customer',
                'description' => 'The phone number of the customer',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'key' => '{customer_tax_id}',
                'label' => 'Customer Tax ID',
                'category' => 'customer',
                'description' => 'The tax identification number of the customer',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'key' => '{invoice_number}',
                'label' => 'Invoice Number',
                'category' => 'invoice',
                'description' => 'The invoice number',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 100,
            ],
            [
                'key' => '{invoice_date}',
                'label' => 'Invoice Issue Date',
                'category' => 'invoice',
                'description' => 'The date the invoice was issued',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 110,
            ],
            [
                'key' => '{invoice_due_date}',
                'label' => 'Invoice Due Date',
                'category' => 'invoice',
                'description' => 'The due date for the invoice payment',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 120,
            ],
            [
                'key' => '{invoice_subtotal}',
                'label' => 'Invoice Subtotal',
                'category' => 'invoice',
                'description' => 'The subtotal amount before tax',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 130,
            ],
            [
                'key' => '{invoice_tax}',
                'label' => 'Invoice Tax Total',
                'category' => 'invoice',
                'description' => 'The total tax amount',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 140,
            ],
            [
                'key' => '{invoice_total}',
                'label' => 'Invoice Total',
                'category' => 'invoice',
                'description' => 'The total amount including tax',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 150,
            ],
            [
                'key' => '{currency_symbol}',
                'label' => 'Currency Symbol',
                'category' => 'invoice',
                'description' => 'The currency symbol for the invoice',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 160,
            ],
            [
                'key' => '{payment_term}',
                'label' => 'Payment Terms',
                'category' => 'payment',
                'description' => 'The payment terms for the invoice',
                'resolver_class' => 'App\Services\PlaceholderResolver',
                'resolver_method' => 'resolveInvoicePlaceholder',
                'applicable_to' => ['invoice_email'],
                'is_active' => true,
                'sort_order' => 200,
            ],
        ];

        foreach ($placeholders as $placeholder) {
            EmailPlaceholder::updateOrCreate(
                ['key' => $placeholder['key']],
                $placeholder
            );
        }
    }
}
