<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Nnjeim\World\Models\Currency;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'currency_id',
        'payment_term_id',
        'number',
        'issue_date',
        'due_date',
        'status',
        'paid_at',
        'voided_at',
        'customer_name',
        'customer_tax_id',
        'customer_email',
        'customer_phone',
        'bill_to_line_1',
        'bill_to_line_2',
        'bill_to_postal_code',
        'bill_to_city',
        'bill_to_state',
        'bill_to_country',
        'subtotal_amount',
        'tax_total_amount',
        'total_amount',
        'total_shipping_amount',
        'total_charges_amount',
        'total_discount_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',

            'customer_id' => 'integer',
            'currency_id' => 'integer',
            'payment_term_id' => 'integer',

            'number' => 'string',
            'issue_date' => 'date',
            'due_date' => 'date',

            'status' => InvoiceStatus::class,

            'customer_name' => 'string',
            'customer_tax_id' => 'string',
            'customer_email' => 'string',
            'customer_phone' => 'string',

            'bill_to_line_1' => 'string',
            'bill_to_line_2' => 'string',
            'bill_to_postal_code' => 'string',
            'bill_to_city' => 'string',
            'bill_to_state' => 'string',
            'bill_to_country' => 'string',

            // Money (minor units)
            'subtotal_amount' => 'decimal:3',
            'tax_total_amount' => 'decimal:3',
            'total_amount' => 'decimal:3',
            'total_shipping_amount' => 'decimal:3',
            'total_charges_amount' => 'decimal:3',
            'total_discount_amount' => 'decimal:3',

            'notes' => 'string',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'paid_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::created(fn () => self::incrementSequence());
    }

    public static function incrementSequence(): void
    {
        $sequence = getInvoiceSequence();
        if (! is_null($sequence)) {
            $sequence->increment();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Nnjeim\World\Models\Currency, $this>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\PaymentTerm, $this>
     */
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\InvoiceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'desc');
    }

    public function recalculateTotals(): void
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        $subtotal = $items->sum(function ($i) {
            /** @var InvoiceItem $i */
            return $i->line_subtotal_amount;
        });

        $taxTotal = $items->sum(function ($i) {
            /** @var InvoiceItem $i */
            return (int) $i->tax_amount;
        });

        $total = $items->sum(fn ($i) => (int) $i->line_total_amount);
        $shippingTotal = $items->sum(fn ($i) => (int) $i->shipping_amount);
        $chargesTotal = $items->sum(fn ($i) => (int) $i->charges_amount);

        $this->forceFill([
            'subtotal_amount' => $subtotal,
            'tax_total_amount' => $taxTotal,
            'total_amount' => $total,
            'total_shipping_amount' => $shippingTotal,
            'total_charges_amount' => $chargesTotal,
        ]);
    }

    protected function getHasItemUnitsAttribute(): bool
    {
        return $this->items->contains(fn ($item) => filled($item->units));
    }

    protected function getHasItemDiscountAttribute(): bool
    {
        return $this->items->contains(fn ($item) => $item->discount_amount > 0);
    }

    protected function getHasItemTaxAttribute(): bool
    {
        return $this->items->contains(fn ($item) => $item->tax_amount > 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\ContactPerson, $this>
     */
    public function contactPeople(): MorphMany
    {
        return $this->morphMany(ContactPerson::class, 'contactable');
    }

    public static function generateUniqueInvoiceId(): string
    {
        if ($sequence = getInvoiceSequence()) {
            return $sequence->getNumber();
        }

        $invoiceId = mb_strtoupper(Str::random(6));
        while (true) {
            $isExist = self::query()->where('number', $invoiceId)->exists();
            if ($isExist) {
                self::generateUniqueInvoiceId();
            }
            break;
        }

        return $invoiceId;
    }

    public function getTotalPaid(): string
    {
        return (string) $this->payments()->sum('amount');
    }

    public function getAmountDue(): string
    {
        $totalPaid = (float) $this->getTotalPaid();
        $totalAmount = (float) $this->total_amount;

        return number_format($totalAmount - $totalPaid, 3, '.', '');
    }

    public function isFullyPaid(): bool
    {
        return (float) $this->getAmountDue() <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        $totalPaid = (float) $this->getTotalPaid();

        return $totalPaid > 0 && ! $this->isFullyPaid();
    }

    public function updatePaymentStatus(): void
    {
        if ($this->isFullyPaid()) {
            $this->update([
                'status' => InvoiceStatus::Paid,
                'paid_at' => now(),
            ]);
        } elseif ($this->isPartiallyPaid()) {
            $this->update([
                'status' => InvoiceStatus::PartialPaid,
            ]);
        }
    }
}
