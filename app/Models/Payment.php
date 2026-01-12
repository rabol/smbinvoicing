<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'payment_method' => PaymentMethod::class,
            'payment_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
