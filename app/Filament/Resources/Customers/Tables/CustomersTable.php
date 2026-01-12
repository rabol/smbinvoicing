<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Tables;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('type')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->icon(fn (CustomerType $state): BackedEnum => match ($state) {
                        CustomerType::Company => Heroicon::BuildingOffice,
                        CustomerType::Person => Heroicon::UserCircle,
                        CustomerType::None => throw new \Exception('To be implemented'),
                    }),

                TextColumn::make('status')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->color(fn (CustomerStatus $state): string => match ($state) {
                        CustomerStatus::None => 'gray',
                        CustomerStatus::Active => 'success',
                        CustomerStatus::Blocked => 'warning',
                        CustomerStatus::Archived => 'danger',
                    })
                    ->badge()
                    ->searchable(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->toggleable()
                    ->icon(Heroicon::Envelope)
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('tax_id')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('locale.name')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('timezone.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paymentTerm.name')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('archived_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                DeleteAction::make()
                    ->visible(fn (Customer $record): bool => $record->invoices()->doesntExist()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
