<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Settings\CompanySettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageCompanySettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = CompanySettings::class;

    #[\Override]
    public static function getNavigationLabel(): string
    {
        return __('Company settings');
    }

    #[\Override]
    public static function getNavigationGroup(): string
    {
        return __('Settings');
    }

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('address_line_1')
                    ->required(),
                TextInput::make('address_line_2'),
                TextInput::make('postal_code')
                    ->required(),
                TextInput::make('country')
                    ->required(),
                TextInput::make('state')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('url')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->telRegex('/^\+?[0-9\s\-\(\)]+$/')
                    ->required(),
                TextInput::make('vat_id'),
                TextInput::make('registration_id'),
                TextInput::make('bank_name'),
                TextInput::make('bank_account'),
                TextInput::make('iban'),
                TextInput::make('bic'),
                TextInput::make('payment_reference'),
                // TextInput::make('logo_path'),
                FileUpload::make('logo_path')->disk('public')->directory('company-settings'),
                TextInput::make('default_currency')
                    ->required(),
            ]);
    }

    public function getRedirectUrl(): string
    {
        return route('filament.dashboard.pages.dashboard');
    }
}
