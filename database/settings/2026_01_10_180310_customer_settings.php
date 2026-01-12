<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('customer.currency_id');
        $this->migrator->add('customer.paymentTerm_id');
        $this->migrator->add('customer.locale_id');
    }
};
