<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContactPerson extends Model
{
    protected $fillable = [
        'add_to_invoice',
        'contactable_id',
        'contactable_type',
        'email',
        'is_primary',
        'name',
        'phone',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'position' => 'string',

            'is_primary' => 'boolean',
            'add_to_invoice' => 'boolean',

            'contactable_id' => 'integer',
            'contactable_type' => 'string',

            // timestamps
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function scopeGlobal($query)
    {
        return $query
            ->whereNull('contactable_type')
            ->whereNull('contactable_id');
    }
}
