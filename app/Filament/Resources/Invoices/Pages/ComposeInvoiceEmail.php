<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Mail\InvoiceMail;
use App\Models\ContactPerson;
use App\Models\Customer;
use App\Models\EmailPlaceholder;
use App\Models\Invoice;
use App\Settings\CompanySettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Storage;

class ComposeInvoiceEmail extends Page implements HasSchemas
{
    use InteractsWithRecord;
    use InteractsWithSchemas;

    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.invoices.pages.compose-invoice-email';

    public array $data = [];

    public function getTitle(): string
    {
        /** @var Invoice $invoice */
        $invoice = $this->record;

        return "Compose Invoice Email - {$invoice->number}";
    }

    public function mount(int|string $record): void
    {
        /** @var Invoice $invoice */
        $invoice = $this->resolveRecord($record);
        $this->record = $invoice;

        $defaultTemplate = 'invoices.email.default';

        // Get email addresses from invoice contact persons
        $invoiceContactEmails = $invoice->contactPeople()
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();

        $this->form->fill([
            'to' => $invoiceContactEmails,
            'subject' => 'Invoice {invoice_number}',
            'template' => $defaultTemplate,
            'message' => "Hello {customer_name},\n\nPlease find your invoice {invoice_number} attached.\n",
        ]);
    }

    public function form(Schema $schema): Schema
    {
        /** @var Invoice $invoice */
        $invoice = $this->record;

        return $schema
            ->statePath('data')
            ->schema([
                Select::make('to')
                    ->label('To')
                    ->multiple()
                    ->options(function () use ($invoice): array {
                        $options = [];

                        // Add contact persons from the invoice
                        $invoiceContacts = $invoice->contactPeople()
                            ->whereNotNull('email')
                            ->get();

                        foreach ($invoiceContacts as $contact) {
                            $options[$contact->email] = "{$contact->name} ({$contact->email}) [Invoice Contact]";
                        }

                        // Add contact persons from the customer
                        $customerContacts = ContactPerson::query()
                            ->where('contactable_type', Customer::class)
                            ->where('contactable_id', $invoice->customer_id)
                            ->whereNotNull('email')
                            ->get();

                        foreach ($customerContacts as $contact) {
                            if (! isset($options[$contact->email])) {
                                $options[$contact->email] = "{$contact->name} ({$contact->email}) [Customer Contact]";
                            }
                        }

                        // Add the customer email if available
                        if ($invoice->customer_email) {
                            $options[$invoice->customer_email] = "{$invoice->customer_name} ({$invoice->customer_email}) [Customer]";
                        }

                        return $options;
                    })
                    ->searchable()
                    ->getOptionLabelsUsing(function (array $values) use ($invoice): array {
                        $labels = [];

                        foreach ($values as $email) {
                            // Try to find a matching contact person from invoice
                            $contact = $invoice->contactPeople()
                                ->where('email', $email)
                                ->first();

                            if ($contact) {
                                $labels[$email] = "{$contact->name} ({$contact->email})";

                                continue;
                            }

                            // Try customer contacts
                            $contact = ContactPerson::query()
                                ->where('contactable_type', Customer::class)
                                ->where('contactable_id', $invoice->customer_id)
                                ->where('email', $email)
                                ->first();

                            if ($contact) {
                                $labels[$email] = "{$contact->name} ({$contact->email})";

                                continue;
                            }

                            // Check if it's the customer email
                            if ($email === $invoice->customer_email) {
                                $labels[$email] = "{$invoice->customer_name} ({$email})";

                                continue;
                            }

                            // Otherwise, it's a manually entered email
                            $labels[$email] = $email;
                        }

                        return $labels;
                    })
                    ->createOptionForm([
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        // Simply return the email address as the "key"
                        return $data['email'];
                    })
                    ->allowHtml()
                    ->required()
                    ->helperText('Select recipients from the list or click "Create" to add a new email address'),

                TextInput::make('subject')
                    ->label('Subject')
                    ->required()
                    ->suffixActions([
                        Action::make('insertPlaceholder')
                            ->label('Insert Placeholder')
                            ->icon('heroicon-o-tag')
                            ->form([
                                Select::make('placeholder')
                                    ->label('Select a placeholder to insert')
                                    ->options(self::getPlaceholders())
                                    ->required()
                                    ->searchable(),
                            ])
                            ->action(function (array $data, Set $set, $state): void {
                                $placeholder = $data['placeholder'] ?? '';
                                $currentValue = $state ?? '';
                                $set('subject', "{$currentValue}{$placeholder}");
                            }),
                    ])
                    ->helperText('Click the tag icon to insert placeholders like {customer_name}, {invoice_number}, etc.'),

                Select::make('template')
                    ->label('HTML template')
                    ->options([
                        'invoices.email.default' => 'Default',
                    ])
                    ->required()
                    ->helperText('Template file lives in resources/views/invoices/email/*.blade.php'),

                Textarea::make('message')
                    ->label('Message')
                    ->rows(12)
                    ->required()
                    ->hintActions([
                        Action::make('insertMessagePlaceholder')
                            ->label('Insert Placeholder')
                            ->icon('heroicon-o-tag')
                            ->form([
                                Select::make('placeholder')
                                    ->label('Select a placeholder to insert')
                                    ->options(self::getPlaceholders())
                                    ->required()
                                    ->searchable(),
                            ])
                            ->action(function (array $data, Set $set, $state): void {
                                $placeholder = $data['placeholder'] ?? '';
                                $currentValue = $state ?? '';
                                $set('message', "{$currentValue}{$placeholder}");
                            }),
                    ])
                    ->helperText('Click the tag icon to insert placeholders. Plain text with line breaks.'),
            ]);
    }

    public function send(): void
    {
        $recipients = $this->data['to'] ?? [];
        $subject = $this->replacePlaceholders((string) ($this->data['subject'] ?? ''));
        $message = $this->replacePlaceholders((string) ($this->data['message'] ?? ''));
        $template = (string) ($this->data['template'] ?? 'invoices.email.default');

        if (empty($recipients)) {
            Notification::make()->title('Missing recipient')->danger()->send();

            return;
        }

        /** @var Invoice $invoice */
        $invoice = $this->record;

        // EXISTING PDF PATH stored on the invoice record
        // Change ONLY the attribute name if yours differs.
        $pdfPath = Storage::disk('invoices')->path(Str::slug($invoice->number).'.pdf');

        // Render the HTML email using the selected Blade template.
        // The template receives:
        // - $invoice, $customer, $message (plain), $messageHtml (escaped + nl2br)
        $customer = $invoice->customer;

        $messageHtml = nl2br(e($message));

        $html = view($template, [
            'invoice' => $invoice,
            'customer' => $customer,
            'message' => $message,
            'messageHtml' => $messageHtml,
        ])->render();

        $logo = resolve(CompanySettings::class)->logo_path;
        $logoPath = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->path($logo) : null;

        // Send the mail with an inline PDF and embedded logo to all recipients
        Mail::to($recipients)->send(new InvoiceMail(
            subject: $subject,
            html: $html,
            pdfPath: $pdfPath,
            filename: $this->pdfFilename(),
            logoPath: $logoPath,
        ));

        $recipientCount = \count($recipients);
        Notification::make()
            ->title("Email sent to {$recipientCount} recipient(s)")
            ->success()
            ->send();
    }

    protected function pdfFilename(): string
    {
        /** @var Invoice $invoice */
        $invoice = $this->record;

        $number = $invoice->number ?? $invoice->id;

        return "invoice-{$number}.pdf";
    }

    protected static function getPlaceholders(): array
    {
        return EmailPlaceholder::query()
            ->active()
            ->forContext('invoice_email')
            ->ordered()
            ->pluck('label', 'key')
            ->toArray();
    }

    protected function replacePlaceholders(string $value): string
    {
        /** @var Invoice $invoice */
        $invoice = $this->record;

        $placeholders = EmailPlaceholder::query()
            ->active()
            ->forContext('invoice_email')
            ->get();

        $replacements = [];
        foreach ($placeholders as $placeholder) {
            $replacements[$placeholder->key] = $placeholder->resolve($invoice);
        }

        return strtr($value, $replacements);
    }
}
