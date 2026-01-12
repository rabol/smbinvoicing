<?php

declare(strict_types=1);

namespace App\Filament\Resources\Countries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('iso2')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('phone_code')
                    ->searchable(),
                TextColumn::make('iso3')
                    ->searchable(),
                TextColumn::make('region')
                    ->searchable(),
                TextColumn::make('subregion')
                    ->searchable(),
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
