<?php

declare(strict_types=1);

namespace App\Models;

use App\Classes\AddressFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\State;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'label',
        'address_line_1',
        'address_line_2',
        'postal_code',
        'country_id',
        'state_id',
        'city_id',
        'state_text',
        'city_text',
        'phone',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Assuming these models exist, or you reference the package namespace models
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Nnjeim\World\Models\Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Nnjeim\World\Models\State, $this>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Nnjeim\World\Models\City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    protected function getFormattedAttribute(): string
    {
        return AddressFormatter::format([
            'name' => $this->customer->name ?? '',
            'company' => $this->label ?? '',
            'street' => $this->address_line_1,
            'street_2' => $this->address_line_2,
            'postal_code' => $this->postal_code,
            'city' => $this->city->name ?? $this->city_text,
            'state' => $this->state->name ?? $this->state_text,
            'province' => $this->state->iso2 ?? '',
            'country' => $this->country->name ?? '',
        ], $this->country->iso2 ?? 'US');
    }
}
