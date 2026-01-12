<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Settings\AuthorizeDocSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageAuthorizeDocSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = AuthorizeDocSettings::class;

    #[\Override]
    public static function getNavigationLabel(): string
    {
        return __('AuthorizeDoc settings');
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
                TextInput::make('api_key'),
                Toggle::make('enabled')
                    ->required(),
                Toggle::make('autoSignInvoice')
                    ->required(),
                Toggle::make('autoSignQuotes')
                    ->required(),
                Toggle::make('autoSignAccountStatements')
                    ->required(),

            ]);
    }

    public function getRedirectUrl(): string
    {
        return route('filament.dashboard.pages.dashboard');
    }
}
