<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('authorizedoc.api_key', '');
        $this->migrator->add('authorizedoc.enabled', 'false');
        $this->migrator->add('authorizedoc.autoSignInvoice', 'false');
        $this->migrator->add('authorizedoc.autoSignQuotes', 'false');
        $this->migrator->add('authorizedoc.autoSignAccountStatements', 'false');
    }
};
