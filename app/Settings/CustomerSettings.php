<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CustomerSettings extends Settings
{
    public ?int $currency_id = null;

    public ?int $paymentTerm_id = null;

    public ?int $locale_id = null;

    public static function group(): string
    {
        return 'customer';
    }
}
