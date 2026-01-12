<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\NumberSequence;
use App\Settings\InvoiceSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageInvoiceSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = InvoiceSettings::class;

    #[\Override]
    public static function getNavigationLabel(): string
    {
        return __('Invoice settings');
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
                Select::make('numbersequence_id')
                    ->label(__('Number Sequence'))
                    ->options(NumberSequence::query()->pluck('type', 'id'))
                    ->required(),
            ]);
    }

    public function getRedirectUrl(): string
    {
        return route('filament.dashboard.pages.dashboard');
    }
}
