<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'taxable',
        'unit',
        'price',
    ];

    /**
     * Attribute casting.
     *
     * Using casts() method (Laravel 10+ recommended style).
     */
    protected function casts(): array
    {
        return [
            'taxable' => 'boolean',
            'price' => 'decimal:3',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
