<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NumberSequenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type'),
                TextEntry::make('pattern'),
                TextEntry::make('prefix')
                    ->placeholder('-'),
                TextEntry::make('postfix')
                    ->placeholder('-'),
                TextEntry::make('delimiter'),
                TextEntry::make('reset_frequency')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('placeholders')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('ordinal_number')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
