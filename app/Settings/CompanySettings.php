<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    public string $name;

    public string $address_line_1;

    public ?string $address_line_2 = null;

    public string $postal_code;

    public string $country;

    public string $state;

    public string $city;

    public string $url;

    public string $email;

    public string $phone;

    public ?string $vat_id = null;

    public ?string $registration_id = null;

    public ?string $bank_name = null;

    public ?string $bank_account = null;

    public ?string $iban = null;

    public ?string $bic = null;

    public ?string $payment_reference = null;

    public ?string $logo_path = null;

    public string $default_currency;

    public static function group(): string
    {
        return 'company';
    }

    #[\Override]
    public static function encrypted(): array
    {
        return [
            'bank_name',
            'bank_account',
            'iban',
        ];
    }
}
