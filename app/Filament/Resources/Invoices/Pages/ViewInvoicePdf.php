<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ViewInvoicePdf extends Page
{
    use InteractsWithRecord;

    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.invoices.pages.view-invoice-pdf';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getPdfUrl(): string
    {
        // Adjust disk/column as needed
        return Storage::disk('invoices')->url(Str::slug($this->record->number).'.pdf');
    }

    #[\Override]
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->icon('heroicon-m-arrow-left')
                ->url(InvoiceResource::getUrl('index')),

        ];
    }
}
