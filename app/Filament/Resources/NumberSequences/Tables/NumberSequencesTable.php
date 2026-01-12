<?php

declare(strict_types=1);

namespace App\Filament\Resources\NumberSequences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NumberSequencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('pattern')
                    ->searchable(),
                TextColumn::make('prefix')
                    ->searchable(),
                TextColumn::make('postfix')
                    ->searchable(),
                TextColumn::make('delimiter')
                    ->searchable(),
                TextColumn::make('reset_frequency')
                    ->badge()
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('ordinal_number')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
