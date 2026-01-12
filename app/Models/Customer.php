<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Nnjeim\World\Models\Currency;
use Nnjeim\World\Models\Timezone;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'name',
        'email',
        'phone',
        'tax_id',
        'locale_id',
        'currency_id',
        'timezone_id',
        'payment_term_id',
        'archived_at',
        'notes',
        'registration_code',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'status' => CustomerStatus::class,
        'type' => CustomerType::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Locale, $this>
     */
    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Nnjeim\World\Models\Currency, $this>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Nnjeim\World\Models\Timezone, $this>
     */
    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\PaymentTerm, $this>
     */
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function addresses(): Customer|HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Address, $this>
     */
    public function companyAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Address, $this>
     */
    public function invoiceAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', 'invoice');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Address, $this>
     */
    public function deliveryAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', 'delivery');
    }

    protected function scopeActive($q)
    {
        return $q->where('status', 'active')->whereNull('archived_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\ContactPerson, $this>
     */
    public function contactPeople(): MorphMany
    {
        return $this->morphMany(ContactPerson::class, 'contactable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }
}
