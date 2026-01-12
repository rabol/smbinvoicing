<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'description',
        'discount_amount',
        'invoice_id',
        'line_subtotal_amount',
        'line_total_amount',
        'name',
        'position',
        'product_id',
        'quantity',
        'tax_amount',
        'tax_rate',
        'unit_price_amount',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'invoice_id' => 'integer',
            'position' => 'integer',
            'product_id' => 'integer',

            'name' => 'string',
            'description' => 'string',
            'unit' => 'string',
            'quantity' => 'decimal:3',
            'tax_rate' => 'decimal:3',

            // money (minor units)
            'unit_price_amount' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'tax_amount' => 'decimal:3',
            'line_subtotal_amount' => 'decimal:3',
            'line_total_amount' => 'decimal:3',
            'shipping_amount' => 'decimal:3',
            'charges_amount' => 'decimal:3',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Net-only, per-line VAT.
     *
     * Rounding: unit_price_amount is already minor-units.
     * We round qty*unit_price_amount to the nearest minor unit using 4th qty.
     */
    public function recalculateLine(int $qtyScale = 4): void
    {
        $qtyStr = (string) $this->quantity; // decimal cast returns string
        $qty = (float) $qtyStr;             // acceptable here due to controlled scale (4dp)

        $unit = $this->unit_price_amount;
        $discount = $this->discount_amount;

        // net before discount in minor units, rounded to the nearest minor unit
        $netBeforeDiscount = round($qty * $unit, 0, PHP_ROUND_HALF_UP);

        $lineNet = max(0, $netBeforeDiscount - $discount);

        $rate = (float) ((string) $this->tax_rate); // percent
        $tax = round($lineNet * ($rate / 100), 0, PHP_ROUND_HALF_UP);

        $this->forceFill([
            'line_subtotal_amount' => $lineNet,
            'tax_amount' => $tax,
            'line_total_amount' => $lineNet + $tax,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
