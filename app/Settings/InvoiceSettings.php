<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InvoiceSettings extends Settings
{
    public ?int $numbersequence_id = null;

    public static function group(): string
    {
        return 'invoice';
    }
}
