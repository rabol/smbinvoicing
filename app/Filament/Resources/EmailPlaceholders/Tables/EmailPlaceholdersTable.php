<?php

declare(strict_types=1);

namespace App\Filament\Resources\EmailPlaceholders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailPlaceholdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Placeholder')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->description(fn ($record) => $record->description),

                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'customer' => 'info',
                        'invoice' => 'success',
                        'payment' => 'warning',
                        'company' => 'danger',
                        'general' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TagsColumn::make('applicable_to')
                    ->label('Applicable To')
                    ->placeholder('All contexts'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'customer' => 'Customer',
                        'invoice' => 'Invoice',
                        'payment' => 'Payment',
                        'company' => 'Company',
                        'general' => 'General',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All placeholders')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
