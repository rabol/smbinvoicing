<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EmailPlaceholder;
use App\Models\NumberSequence;
use App\Settings\AuthorizeDocSettings;
use App\Settings\CompanySettings;
use App\Settings\CustomerSettings;
use App\Settings\InvoiceSettings;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConfigurationStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    #[\Override]
    protected function getStats(): array
    {
        return [
            $this->getCompanySettingsStat(),
            $this->getInvoiceSettingsStat(),
            $this->getCustomerSettingsStat(),
            $this->getNumberSequencesStat(),
            $this->getEmailPlaceholdersStat(),
            $this->getAuthorizeDocSettingsStat(),
        ];
    }

    private function getCompanySettingsStat(): Stat
    {
        $settings = resolve(CompanySettings::class);
        $issues = [];

        if (empty($settings->name)) {
            $issues[] = 'Company name';
        }

        if (empty($settings->address_line_1)) {
            $issues[] = 'Address';
        }

        if (empty($settings->postal_code)) {
            $issues[] = 'Postal code';
        }

        if (empty($settings->city)) {
            $issues[] = 'City';
        }

        if (empty($settings->country)) {
            $issues[] = 'Country';
        }

        if (empty($settings->email)) {
            $issues[] = 'Email';
        }

        if (empty($settings->phone)) {
            $issues[] = 'Phone';
        }

        if (! $settings->logo_path) {
            $issues[] = 'Logo';
        }

        if (empty($settings->default_currency)) {
            $issues[] = 'Default currency';
        }

        $isComplete = empty($issues);

        return Stat::make('Company Settings', $isComplete ? 'Configured' : 'Incomplete')
            ->description($isComplete ? 'All settings configured' : 'Missing: '.implode(', ', $issues))
            ->descriptionIcon($isComplete ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle)
            ->color($isComplete ? 'success' : 'warning')
            ->url(route('filament.dashboard.pages.manage-company-settings'));
    }

    private function getInvoiceSettingsStat(): Stat
    {
        $settings = resolve(InvoiceSettings::class);
        $issues = [];

        if (! $settings->numbersequence_id) {
            $issues[] = 'Number sequence';
        }

        $isComplete = empty($issues);

        return Stat::make('Invoice Settings', $isComplete ? 'Configured' : 'Incomplete')
            ->description($isComplete ? 'All settings configured' : 'Missing: '.implode(', ', $issues))
            ->descriptionIcon($isComplete ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle)
            ->color($isComplete ? 'success' : 'warning')
            ->url(route('filament.dashboard.pages.manage-invoice-settings'));
    }

    private function getCustomerSettingsStat(): Stat
    {
        $settings = resolve(CustomerSettings::class);
        $issues = [];

        if (! $settings->currency_id) {
            $issues[] = 'Default currency';
        }

        if (! $settings->paymentTerm_id) {
            $issues[] = 'Default payment term';
        }

        if (! $settings->locale_id) {
            $issues[] = 'Default locale';
        }

        $isComplete = empty($issues);

        return Stat::make('Customer Settings', $isComplete ? 'Configured' : 'Incomplete')
            ->description($isComplete ? 'All settings configured' : 'Missing: '.implode(', ', $issues))
            ->descriptionIcon($isComplete ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle)
            ->color($isComplete ? 'success' : 'warning')
            ->url(route('filament.dashboard.pages.manage-customer-settings'));
    }

    private function getNumberSequencesStat(): Stat
    {
        $count = NumberSequence::count();
        $isComplete = $count > 0;

        return Stat::make('Number Sequences', $isComplete ? $count : 'None')
            ->description($isComplete ? 'Number sequences configured' : 'No number sequences created')
            ->descriptionIcon($isComplete ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle)
            ->color($isComplete ? 'success' : 'warning')
            ->url(route('filament.dashboard.resources.number-sequences.index'));
    }

    private function getEmailPlaceholdersStat(): Stat
    {
        $count = EmailPlaceholder::count();
        $activeCount = EmailPlaceholder::active()->count();
        $isComplete = $count > 0;

        return Stat::make('Email Placeholders', $isComplete ? "{$activeCount}/{$count}" : 'None')
            ->description($isComplete ? "{$activeCount} active out of {$count} total" : 'No email placeholders created')
            ->descriptionIcon($isComplete ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle)
            ->color($isComplete ? 'success' : 'warning')
            ->url(route('filament.dashboard.resources.email-placeholders.index'));
    }

    private function getAuthorizeDocSettingsStat(): Stat
    {
        $settings = resolve(AuthorizeDocSettings::class);
        $issues = [];

        if ($settings->enabled && empty($settings->api_key)) {
            $issues[] = 'API key required when enabled';
        }

        $isComplete = ! $settings->enabled || empty($issues);

        return Stat::make('AuthorizeDoc Settings', $settings->enabled ? ($isComplete ? 'Configured' : 'Incomplete') : 'Disabled')
            ->description(
                ! $settings->enabled
                    ? 'Service is disabled'
                    : ($isComplete ? 'All settings configured' : 'Missing: '.implode(', ', $issues))
            )
            ->descriptionIcon($settings->enabled ? ($isComplete ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle) : Heroicon::MinusCircle)
            ->color($settings->enabled ? ($isComplete ? 'success' : 'warning') : 'gray')
            ->url(route('filament.dashboard.pages.manage-authorize-doc-settings'));
    }
}
