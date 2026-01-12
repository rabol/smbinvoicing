<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'draft';
        $data['number'] = $this->generateInvoiceNumber($data['customer_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Invoice $record */
        $record = $this->record;

    }

    protected function generateInvoiceNumber(int $customerId): string
    {
        return Invoice::generateUniqueInvoiceId();
    }
}
