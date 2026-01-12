<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.name', 'Acme');
        $this->migrator->add('company.address_line_1', 'Acme Street');
        $this->migrator->add('company.address_line_2', null);
        $this->migrator->add('company.postal_code', '007');
        $this->migrator->add('company.country', 'Fantasy');
        $this->migrator->add('company.state', 'Rough');
        $this->migrator->add('company.city', 'Gotham');
        $this->migrator->add('company.url', 'acme.com');
        $this->migrator->add('company.email', 'hello@acme.com');
        $this->migrator->add('company.phone', '555-555-555');
        $this->migrator->add('company.vat_id', null);
        $this->migrator->add('company.registration_id', null);
        $this->migrator->add('company.bank_name', null);
        $this->migrator->add('company.bank_account', null);
        $this->migrator->add('company.iban', null);
        $this->migrator->add('company.bic', null);
        $this->migrator->add('company.payment_reference', null);
        $this->migrator->add('company.logo_path', null);
        $this->migrator->add('company.default_currency', 'EUR');
    }
};
