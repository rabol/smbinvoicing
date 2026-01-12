<?php

declare(strict_types=1);

use App\Filament\Widgets\ConfigurationStatusWidget;
use App\Models\EmailPlaceholder;
use App\Models\NumberSequence;
use App\Models\User;
use App\Settings\AuthorizeDocSettings;
use App\Settings\CompanySettings;
use App\Settings\CustomerSettings;
use App\Settings\InvoiceSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('displays company settings status as incomplete when required fields are missing', function () {
    $settings = app(CompanySettings::class);
    $settings->name = '';
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Company Settings')
        ->assertSee('Incomplete')
        ->assertSee('Company name');
});

it('displays company settings status as configured when all fields are filled', function () {
    $settings = app(CompanySettings::class);
    $settings->name = 'Test Company';
    $settings->address_line_1 = '123 Main St';
    $settings->postal_code = '12345';
    $settings->city = 'Test City';
    $settings->country = 'Test Country';
    $settings->email = 'test@example.com';
    $settings->phone = '1234567890';
    $settings->logo_path = 'path/to/logo.png';
    $settings->default_currency = 'USD';
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Company Settings')
        ->assertSee('Configured')
        ->assertSee('All settings configured');
});

it('displays invoice settings status as incomplete when number sequence is missing', function () {
    $settings = app(InvoiceSettings::class);
    $settings->numbersequence_id = null;
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Invoice Settings')
        ->assertSee('Incomplete')
        ->assertSee('Number sequence');
});

it('displays customer settings status as incomplete when required fields are missing', function () {
    $settings = app(CustomerSettings::class);
    $settings->currency_id = null;
    $settings->paymentTerm_id = null;
    $settings->locale_id = null;
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Customer Settings')
        ->assertSee('Incomplete')
        ->assertSee('Default currency');
});

it('displays authorize doc settings as disabled when not enabled', function () {
    $settings = app(AuthorizeDocSettings::class);
    $settings->enabled = false;
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('AuthorizeDoc Settings')
        ->assertSee('Disabled')
        ->assertSee('Service is disabled');
});

it('displays authorize doc settings as incomplete when enabled but missing api key', function () {
    $settings = app(AuthorizeDocSettings::class);
    $settings->enabled = true;
    $settings->api_key = null;
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('AuthorizeDoc Settings')
        ->assertSee('Incomplete')
        ->assertSee('API key required');
});

it('displays authorize doc settings as configured when enabled with api key', function () {
    $settings = app(AuthorizeDocSettings::class);
    $settings->enabled = true;
    $settings->api_key = 'test-api-key';
    $settings->save();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('AuthorizeDoc Settings')
        ->assertSee('Configured')
        ->assertSee('All settings configured');
});

it('displays number sequences status as incomplete when none exist', function () {
    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Number Sequences')
        ->assertSee('None')
        ->assertSee('No number sequences created');
});

it('displays number sequences count when sequences exist', function () {
    NumberSequence::factory()->count(3)->create();

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Number Sequences')
        ->assertSee('3')
        ->assertSee('Number sequences configured');
});

it('displays email placeholders status as incomplete when none exist', function () {
    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Email Placeholders')
        ->assertSee('None')
        ->assertSee('No email placeholders created');
});

it('displays email placeholders count when placeholders exist', function () {
    EmailPlaceholder::factory()->count(5)->create(['is_active' => true]);
    EmailPlaceholder::factory()->count(2)->create(['is_active' => false]);

    Livewire::test(ConfigurationStatusWidget::class)
        ->assertSee('Email Placeholders')
        ->assertSee('5/7')
        ->assertSee('5 active out of 7 total');
});
