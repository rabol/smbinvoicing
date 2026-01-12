<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPeople\Tables;

use App\Models\ContactPerson;
use App\Models\Customer;
use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactPeopleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('phone'),

                TextColumn::make('position'),

                IconColumn::make('is_primary')
                    ->boolean()
                    ->label('Primary'),

                TextColumn::make('contactable_type')
                    ->label('Scope')
                    ->formatStateUsing(function (?string $state, ContactPerson $record) {
                        if (! $state) {
                            return 'Global';
                        }

                        $short = class_basename($state);

                        return $short.' #'.$record->contactable_id;
                    }),
            ])
            ->filters([
                SelectFilter::make('contactable_type')
                    ->label('Scope')
                    ->options([
                        'global' => 'Global only',
                        Customer::class => 'Customer',
                        Invoice::class => 'Invoice',
                    ])
                    ->query(function (Builder $query, $value) {
                        if ($value === 'global') {
                            return $query
                                ->whereNull('contactable_type')
                                ->whereNull('contactable_id');
                        }

                        if ($value) {
                            return $query->where('contactable_type', $value);
                        }

                        return $query;
                    }),
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
