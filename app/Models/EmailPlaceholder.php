<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailPlaceholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'category',
        'description',
        'resolver_class',
        'resolver_method',
        'applicable_to',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'applicable_to' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function scopeForContext($query, string $context)
    {
        return $query->where(function ($q) use ($context): void {
            $q->whereJsonContains('applicable_to', $context)
                ->orWhereNull('applicable_to');
        });
    }

    protected function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    protected function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    public function resolve(mixed $data): string
    {
        if ($this->resolver_class && $this->resolver_method) {
            $resolverClass = resolve($this->resolver_class);
            $method = $this->resolver_method;

            return (string) $resolverClass->{$method}($data, $this->key);
        }

        return '';
    }
}
