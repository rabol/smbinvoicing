<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Tables;

use App\Actions\AddDigitalSignatureToInvoice;
use App\Classes\PdfInvoiceA4Paper;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\CreatePdfForInvoiceService;
use App\Settings\AuthorizeDocSettings;
use App\Settings\CompanySettings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (InvoiceStatus $state): string => match ($state) {
                        InvoiceStatus::None => 'gray',
                        InvoiceStatus::Draft, InvoiceStatus::InvoiceCreated => 'info',
                        InvoiceStatus::InvoiceSend, InvoiceStatus::Invoiced => 'success',
                        InvoiceStatus::PartialPaid => 'warning',
                        InvoiceStatus::Paid => 'success',
                        InvoiceStatus::Void => 'danger',
                    }),
                TextColumn::make('issue_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->money(fn ($record) => $record?->currency?->code ?? 'USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('create_invoice')
                        ->icon(Heroicon::WrenchScrewdriver)
                        ->requiresConfirmation()
                        ->hidden(fn ($record) => ! self::canCreateInvoice($record))
                        ->action(fn ($record) => self::createInvoice($record)),

                    Action::make('add_digital_signature')
                        ->icon(Heroicon::PencilSquare)
                        ->requiresConfirmation()
                        ->hidden(fn ($record) => ! self::canAddDigitalSignature($record))
                        ->action(fn ($record) => self::addDigitalSignature($record)),

                    Action::make('delete_invoice')
                        ->label(__('delete PDF'))
                        ->icon(Heroicon::XMark)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->hidden(fn ($record) => ! self::canDeleteInvoice($record))
                        ->action(fn ($record) => self::deleteInvoice($record)),

                    Action::make('send_invoice')
                        ->icon(Heroicon::PaperAirplane)
                        ->color('success')
                        ->requiresConfirmation()
                        ->hidden(fn ($record) => ! self::canSendInvoice($record))
                        ->url(fn (Invoice $record) => InvoiceResource::getUrl('compose-email', ['record' => $record])),

                    Action::make('record_payment')
                        ->label('Record Payment')
                        ->icon(Heroicon::OutlinedBanknotes)
                        ->color('success')
                        ->hidden(fn (Invoice $record) => ! self::canRecordPayment($record))
                        ->form(fn (Invoice $record) => [
                            TextInput::make('amount')
                                ->required()
                                ->numeric()
                                ->default($record->getAmountDue())
                                ->prefix($record->currency->symbol ?? '$')
                                ->step('0.001')
                                ->helperText(self::getPaymentHelper($record)),
                            Select::make('payment_method')
                                ->options(PaymentMethod::class)
                                ->required()
                                ->label('Payment Method')
                                ->native(false),
                            DatePicker::make('payment_date')
                                ->required()
                                ->default(now())
                                ->label('Payment Date')
                                ->native(false),
                            TextInput::make('reference_number')
                                ->maxLength(255)
                                ->label('Reference Number'),
                            TextInput::make('transaction_id')
                                ->maxLength(255)
                                ->label('Transaction ID'),
                            Textarea::make('notes')
                                ->rows(3),
                        ])
                        ->action(function (Invoice $record, array $data): void {
                            Payment::create([
                                'invoice_id' => $record->id,
                                'amount' => $data['amount'],
                                'payment_method' => $data['payment_method'],
                                'payment_date' => $data['payment_date'],
                                'reference_number' => $data['reference_number'] ?? null,
                                'transaction_id' => $data['transaction_id'] ?? null,
                                'notes' => $data['notes'] ?? null,
                            ]);

                            $record->updatePaymentStatus();

                            Notification::make()
                                ->title('Payment recorded successfully')
                                ->success()
                                ->send();
                        }),

                    Action::make('pdf')
                        ->label('View PDF')
                        ->icon('heroicon-o-document-text')
                        ->hidden(fn ($record) => ! self::canViewInvoice($record))
                        ->action(function ($record, Action $action): void {

                            if (! Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
                                Notification::make()
                                    ->title('PDF not available')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $action->redirect(
                                InvoiceResource::getUrl('pdf', [
                                    'record' => $record,
                                ])
                            );
                        }),

                    Action::make('download_pdf')
                        ->label('Download PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->hidden(fn ($record) => ! self::canViewInvoice($record))
                        ->action(function ($record, Action $action) {

                            if (! Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
                                Notification::make()
                                    ->title('PDF not available')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            return Storage::disk('invoices')->download(Str::slug($record->number).'.pdf');
                        }),
                    EditAction::make()->hidden(fn ($record) => ! self::canEditInvoice($record)),
                    DeleteAction::make()
                        ->hidden(fn ($record) => $record && (
                            $record->status === InvoiceStatus::Invoiced ||
                            $record->status === InvoiceStatus::PartialPaid ||
                            $record->status === InvoiceStatus::Paid ||
                            $record->status === InvoiceStatus::Void
                        )),

                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function canEditInvoice(?Invoice $record)
    {
        if (! $record) {
            return false;
        }

        if ($record->status == InvoiceStatus::Draft || $record->status == InvoiceStatus::InvoiceCreated) {
            return true;
        }

    }

    private static function canCreateInvoice(Invoice $record): bool
    {
        if ($record->status == InvoiceStatus::Draft) {
            return true;
        }

        return false;
    }

    private static function createInvoice(Invoice $record): void
    {
        // Create a PDF invoice
        try {
            // Get company settings
            $logo = resolve(CompanySettings::class)->logo_path;

            $service = CreatePdfForInvoiceService::make($record)
                ->setPaperOptions(new PdfInvoiceA4Paper)
                ->setDateFormat('d-m-Y');

            if ($logo) {
                $service->setLogo(Storage::disk('public')->path($logo));
            }

            $saved = $service->setTemplate('default-new')
                ->save('invoices');

            if ($saved) {
                Notification::make()
                    ->title('Your PDF invoice was created')
                    ->success()
                    ->send();
                /** @phpstan-ignore assign.propertyType */
                $record->status = InvoiceStatus::InvoiceCreated;
                $record->save();

            } else {
                Notification::make()
                    ->title('Could not create PDF invoice')
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Notification::make()
                ->title('Error creating PDF')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private static function canAddDigitalSignature(Invoice $record): bool
    {

        // First check if AuthorizeDoc is enable
        if (resolve(AuthorizeDocSettings::class)->enabled) {
            if (! resolve(AuthorizeDocSettings::class)->autoSignInvoice) { // if Auto sign is turned of, show the menu
                if ($record->status == InvoiceStatus::InvoiceCreated) {
                    if (Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {

                        if (pdfHasDigitalSignature(Storage::disk('invoices')->path(Str::slug($record->number).'.pdf'))) {
                            return false;
                        }

                        return true;
                    }
                }
            }
        }

        return false;
    }

    private static function addDigitalSignature(Invoice $record)
    {
        try {

            if (pdfHasDigitalSignature(Storage::disk('invoices')->path(Str::slug($record->number).'.pdf'))) {
                Notification::make()
                    ->title('Error adding digital signature to PDF')
                    ->body('PDF already have digital signature')
                    ->danger()
                    ->send();

                return;
            }

            AddDigitalSignatureToInvoice::make()->handle(Str::slug($record->number).'.pdf');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Notification::make()
                ->title('Error adding digital signature to PDF')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

    }

    private static function canSendInvoice(Invoice $record): bool
    {
        if ($record->status === InvoiceStatus::InvoiceCreated) {

            if (Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
                return true;
            }
        }

        return false;
    }

    private static function canViewInvoice(Invoice $record): bool
    {
        if ($record->status == InvoiceStatus::InvoiceCreated ||
            $record->status == InvoiceStatus::InvoiceSend ||
            $record->status == InvoiceStatus::Invoiced ||
            $record->status == InvoiceStatus::PartialPaid ||
            $record->status == InvoiceStatus::Paid ||
            $record->status == InvoiceStatus::Void) {
            if (Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
                return true;
            } else {

                Notification::make()
                    ->title('View PDF')
                    ->body(__('Could not find :filename', ['filename' => Str::slug($record->number).'.pdf']))
                    ->danger()
                    ->send();
            }
        }

        return false;
    }

    private static function deleteInvoice(Invoice $record): void
    {
        if (Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
            Storage::disk('invoices')->delete(Str::slug($record->number).'.pdf');
            Notification::make()
                ->title('Delete invoice')
                ->body(__('The invoice :filename is deleted', ['filename' => Str::slug($record->number).'.pdf']))
                ->danger()
                ->send();

            /** @phpstan-ignore assign.propertyType */
            $record->status = InvoiceStatus::Draft;
            $record->save();

            return;
        }

        Notification::make()
            ->title('Error finding PDF')
            ->body(__('Could not find :filename', ['filename' => Str::slug($record->number).'.pdf']))
            ->danger()
            ->send();
    }

    private static function canDeleteInvoice(Invoice $record): bool
    {
        if ($record->status == InvoiceStatus::InvoiceCreated) {
            if (Storage::disk('invoices')->exists(Str::slug($record->number).'.pdf')) {
                return true;
            }

            return true;
        }

        return false;
    }

    private static function canRecordPayment(Invoice $record): bool
    {
        if ($record->status === InvoiceStatus::Invoiced ||
            $record->status === InvoiceStatus::PartialPaid ||
            $record->status === InvoiceStatus::Paid) {
            if (! $record->isFullyPaid()) {
                return true;
            }
        }

        return false;
    }

    private static function getPaymentHelper(Invoice $record): string
    {
        $totalAmount = (float) $record->total_amount;
        $totalPaid = (float) $record->getTotalPaid();
        $amountDue = (float) $record->getAmountDue();
        $symbol = $record->currency->symbol ?? '$';

        if ($totalPaid > 0) {
            return "Invoice Total: {$symbol}{$totalAmount} | Paid: {$symbol}{$totalPaid} | Due: {$symbol}{$amountDue}";
        }

        return "Invoice Total: {$symbol}{$totalAmount} | Amount Due: {$symbol}{$amountDue}";
    }
}
