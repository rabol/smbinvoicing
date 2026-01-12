<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Classes\PdfInvoiceA4Paper;
use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTerm;
use App\Services\CreatePdfForInvoiceService;
use Exception;
use Illuminate\Database\Seeder;
use Nnjeim\World\Models\Currency;

class InvoiceSeeder extends Seeder
{
    public int $invNum = 0;

    public function run(): void
    {
        Customer::query()
            ->with([
                'invoiceAddress.country',
                'invoiceAddress.state.country',
                'invoiceAddress.city.state.country',
            ])
            ->chunkById(200, function ($customers) {
                foreach ($customers as $customer) {
                    $invoiceCount = random_int(1, 5);

                    $currency = $this->resolveCurrencyForCustomer($customer);
                    $paymentTermId = $customer->payment_term_id
                        ?? PaymentTerm::query()->inRandomOrder()->value('id');

                    for ($i = 0; $i < $invoiceCount; $i++) {
                        $addr = $customer->invoiceAddress;

                        // Make sure location hierarchy is respected
                        $city = $addr?->city;
                        $state = $city?->state ?? $addr?->state;
                        $country = $state?->country ?? $addr?->country;

                        $issueDate = now()->subDays(random_int(0, 180))->toDateString();
                        $dueDate = now()->parse($issueDate)->addDays(random_int(0, 60))->toDateString();
                        $status = collect(['draft', 'invoice_created', 'invoiced', 'invoice_send', 'partial_paid', 'paid', 'void'])->random();

                        $invoice = Invoice::create([
                            'customer_id' => $customer->id,
                            'currency_id' => $currency->id,
                            'payment_term_id' => $paymentTermId,

                            'number' => $this->makeInvoiceNumber($customer->id),
                            'issue_date' => $issueDate,
                            'due_date' => $dueDate,

                            'status' => $status,
                            'paid_at' => $status === 'paid' ? now()->subDays(random_int(0, 60)) : null,
                            'voided_at' => $status === 'void' ? now()->subDays(random_int(0, 60)) : null,

                            // Customer snapshot
                            'customer_name' => (string) $customer->name,
                            'customer_tax_id' => $customer->tax_id,
                            'customer_email' => $customer->email,
                            'customer_phone' => $customer->phone,

                            // Billing address snapshot (respect city -> state -> country chain)
                            'bill_to_line_1' => $addr?->address_line_1,
                            'bill_to_line_2' => $addr?->address_line_2,
                            'bill_to_postal_code' => $addr?->postal_code,
                            'bill_to_city' => $city?->name ?? $addr?->city_text,
                            'bill_to_state' => $state?->name ?? $addr?->state_text,
                            'bill_to_country' => $country?->name,

                            // Totals (decimal(8,2) in major units)
                            'subtotal_amount' => '0.00',
                            'tax_total_amount' => '0.00',
                            'total_amount' => '0.00',
                            'total_discount_amount' => '0.00',
                            'notes' => null,
                        ]);

                        $lineCount = random_int(1, 10);

                        for ($pos = 1; $pos <= $lineCount; $pos++) {
                            $qty = $this->randDecimalString(1, 10, 4);

                            // Generate unit prices "5..250" in major units as decimal(8,2)
                            $unitPriceAmount = $this->randMoneyAmount(5, 250);

                            // Occasionally apply a discount up to "20" major units
                            $discountAmount = (random_int(0, 3) === 0)
                                ? $this->randMoneyAmount(0, 20)
                                : '0.00';

                            $taxRate = (string) collect([0, 5, 7, 9, 10, 19, 20, 21, 22, 25])->random();

                            $item = new InvoiceItem([
                                'position' => $pos,
                                'name' => "Item {$pos}",
                                'description' => random_int(0, 1) ? 'Service description' : 'Product description',

                                'quantity' => $qty,

                                'unit_price_amount' => $unitPriceAmount,
                                'discount_amount' => $discountAmount,

                                'tax_rate' => $taxRate,

                                // computed (decimal 8,2)
                                'tax_amount' => '0.00',
                                'line_subtotal_amount' => '0.00',
                                'line_total_amount' => '0.00',
                            ]);

                            $item->invoice()->associate($invoice);
                            $item->recalculateLine();
                            $item->save();
                        }

                        $invoice->load('items');
                        $invoice->recalculateTotals();
                        $invoice->save();

                        if ($invoice->status !== InvoiceStatus::Draft) {
                            $saved = CreatePdfForInvoiceService::make($invoice)
                                ->setPaperOptions(new PdfInvoiceA4Paper)
                                ->setDateFormat('d-m-Y')
                                ->setLogo(public_path('images/invoices/default/logo-color2.png'))
                                ->setTemplate('default-new')
                                ->save('invoices');

                            if (! $saved) {
                                throw new Exception('Did not save pdf');
                            }
                        }
                    }
                }
            });
    }

    private function resolveCurrencyForCustomer(Customer $customer): Currency
    {
        if ($customer->currency_id) {
            $found = Currency::query()->find($customer->currency_id);
            if ($found) {
                return $found;
            }
        }

        // Fallback: any currency in the world table
        return Currency::query()->inRandomOrder()->firstOrFail();
    }

    private function makeInvoiceNumber(int $customerId): string
    {
        $this->invNum++;

        return 'INV-'

            .now()->format('Y')
            .'-'
            .str_pad((string) sprintf('%d', $this->invNum), 6, '0', STR_PAD_LEFT);

    }

    /**
     * Generates a money amount as a decimal(8,2) string in MAJOR UNITS.
     * Example: "123.45".
     */
    private function randMoneyAmount(int $minMajor, int $maxMajor): string
    {
        $minCents = $minMajor * 100;
        $maxCents = $maxMajor * 100;

        $intVal = random_int($minCents, $maxCents);

        return number_format($intVal / 100, 2, '.', '');
    }

    private function randDecimalString(int $min, int $max, int $scale): string
    {
        $factor = 10 ** $scale;
        $intVal = random_int($min * $factor, $max * $factor);

        return number_format($intVal / $factor, $scale, '.', '');
    }
}
