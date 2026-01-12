<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AuthorizeDocSettings extends Settings
{
    public ?string $api_key = null;

    public bool $enabled = false;

    public bool $autoSignInvoice = false;

    public bool $autoSignQuotes = false;

    public bool $autoSignAccountStatements = false;

    public static function group(): string
    {
        return 'authorizedoc';
    }
}
