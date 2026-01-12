<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Locale;
use App\Models\PaymentTerm;
use App\Settings\CustomerSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Nnjeim\World\Models\Currency;

class ManageCustomerSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = CustomerSettings::class;

    #[\Override]
    public static function getNavigationLabel(): string
    {
        return __('Customer settings');
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

                Select::make('currency_id')
                    ->label(__('Currency'))
                    ->options(Currency::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('paymentTerm_id')
                    ->label(__('Payment term'))
                    ->options(PaymentTerm::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('locale_id')
                    ->label(__('Locale'))
                    ->options(Locale::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

            ]);
    }

    public function getRedirectUrl(): string
    {
        return route('filament.dashboard.pages.dashboard');
    }
}
