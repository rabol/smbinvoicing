<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ComposeInvoiceEmail;
use App\Filament\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Resources\Invoices\Schemas\InvoiceInfolist;
use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit' => EditInvoice::route('/{record}/edit'),
            'view' => ViewInvoice::route('/{record}'),
            // Custom page for PDF
            'pdf' => Pages\ViewInvoicePdf::route('/{record}/pdf'),
            'compose-email' => ComposeInvoiceEmail::route('/{record}/compose-email'),
        ];
    }
}
