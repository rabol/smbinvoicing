<?php

declare(strict_types=1);

namespace App\Filament\Resources\States;

use App\Filament\Resources\States\Pages\CreateState;
use App\Filament\Resources\States\Pages\EditState;
use App\Filament\Resources\States\Pages\ListStates;
use App\Filament\Resources\States\Pages\ViewState;
use App\Filament\Resources\States\Schemas\StateForm;
use App\Filament\Resources\States\Schemas\StateInfolist;
use App\Filament\Resources\States\Tables\StatesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Nnjeim\World\Models\State;
use UnitEnum;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('States');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('general.system_address');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return StateForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return StateInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return StatesTable::configure($table);
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
            'index' => ListStates::route('/'),
            'create' => CreateState::route('/create'),
            'view' => ViewState::route('/{record}'),
            'edit' => EditState::route('/{record}/edit'),
        ];
    }
}
