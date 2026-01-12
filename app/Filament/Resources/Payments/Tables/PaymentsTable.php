<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.number')
                    ->searchable()
                    ->sortable()
                    ->label('Invoice #'),
                TextColumn::make('invoice.customer_name')
                    ->searchable()
                    ->sortable()
                    ->label('Customer')
                    ->limit(30),
                TextColumn::make('amount')
                    ->money('USD', divideBy: 1)
                    ->sortable()
                    ->label('Amount'),
                TextColumn::make('payment_method')
                    ->badge()
                    ->searchable()
                    ->label('Method'),
                TextColumn::make('payment_date')
                    ->date()
                    ->sortable()
                    ->label('Date'),
                TextColumn::make('reference_number')
                    ->searchable()
                    ->toggleable()
                    ->label('Reference'),
                TextColumn::make('transaction_id')
                    ->searchable()
                    ->toggleable()
                    ->label('Transaction ID'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('payment_date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
