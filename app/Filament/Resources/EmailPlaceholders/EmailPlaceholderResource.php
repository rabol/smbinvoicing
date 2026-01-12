<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailPlaceholders;

use App\Filament\Resources\EmailPlaceholders\Pages\CreateEmailPlaceholder;
use App\Filament\Resources\EmailPlaceholders\Pages\EditEmailPlaceholder;
use App\Filament\Resources\EmailPlaceholders\Pages\ListEmailPlaceholders;
use App\Filament\Resources\EmailPlaceholders\Schemas\EmailPlaceholderForm;
use App\Filament\Resources\EmailPlaceholders\Tables\EmailPlaceholdersTable;
use App\Models\EmailPlaceholder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailPlaceholderResource extends Resource
{
    protected static ?string $model = EmailPlaceholder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 99;

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return EmailPlaceholderForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return EmailPlaceholdersTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailPlaceholders::route('/'),
            'create' => CreateEmailPlaceholder::route('/create'),
            'edit' => EditEmailPlaceholder::route('/{record}/edit'),
        ];
    }
}
