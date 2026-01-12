<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences;

use App\Filament\Resources\NumberSequences\Pages\CreateNumberSequence;
use App\Filament\Resources\NumberSequences\Pages\EditNumberSequence;
use App\Filament\Resources\NumberSequences\Pages\ListNumberSequences;
use App\Filament\Resources\NumberSequences\Pages\ViewNumberSequence;
use App\Filament\Resources\NumberSequences\Schemas\NumberSequenceForm;
use App\Filament\Resources\NumberSequences\Schemas\NumberSequenceInfolist;
use App\Filament\Resources\NumberSequences\Tables\NumberSequencesTable;
use App\Models\NumberSequence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;

class NumberSequenceResource extends Resource
{
    protected static ?string $model = NumberSequence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'type';

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('Number sequences');
    }

    #[Override]
    public static function getTitleCasePluralModelLabel(): string
    {
        return __('Number sequences');
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return NumberSequenceForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return NumberSequenceInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return NumberSequencesTable::configure($table);
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
            'index' => ListNumberSequences::route('/'),
            'create' => CreateNumberSequence::route('/create'),
            'view' => ViewNumberSequence::route('/{record}'),
            'edit' => EditNumberSequence::route('/{record}/edit'),
        ];
    }
}
