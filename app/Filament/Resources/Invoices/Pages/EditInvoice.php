<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[\Override]
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;

    }

    #[\Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Invoice $record */
        $record = $this->record;
        /** @phpstan-ignore assign.propertyType */
        $record->status = InvoiceStatus::Draft;
        $record->save();

        if (Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
            Storage::disk('invoices')->delete(Str::slug($record->number).'.pdf');
        }
    }
}
