<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople;

use App\Filament\Resources\ContactPeople\Pages\CreateContactPerson;
use App\Filament\Resources\ContactPeople\Pages\EditContactPerson;
use App\Filament\Resources\ContactPeople\Pages\ListContactPeople;
use App\Filament\Resources\ContactPeople\Pages\ViewContactPerson;
use App\Filament\Resources\ContactPeople\Schemas\ContactPersonForm;
use App\Filament\Resources\ContactPeople\Schemas\ContactPersonInfolist;
use App\Filament\Resources\ContactPeople\Tables\ContactPeopleTable;
use App\Models\ContactPerson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactPersonResource extends Resource
{
    protected static ?string $model = ContactPerson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return ContactPersonForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return ContactPersonInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return ContactPeopleTable::configure($table);
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
            'index' => ListContactPeople::route('/'),
            'create' => CreateContactPerson::route('/create'),
            'view' => ViewContactPerson::route('/{record}'),
            'edit' => EditContactPerson::route('/{record}/edit'),
        ];
    }
}
