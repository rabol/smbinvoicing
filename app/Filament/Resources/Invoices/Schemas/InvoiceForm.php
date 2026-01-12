<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use App\Models\ContactPerson;
use App\Models\Customer;
use App\Models\PaymentTerm;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Nnjeim\World\Models\Currency;

class InvoiceForm
{
    public static ?int $cachedCurrencyId = null;

    public static ?Currency $cachedCurrency = null;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Details')
                    ->description('Select a customer and manage their contact information.')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => self::handleCustomerUpdate($set, $get, $state)),

                                TextInput::make('customer_name')
                                    ->required(),

                                TextInput::make('customer_tax_id')
                                    ->label('Tax ID')
                                    ->readOnly(),

                                TextInput::make('customer_email')
                                    ->email(),

                                TextInput::make('customer_phone')
                                    ->tel()
                                    ->telRegex('/^\+?[0-9\s\-\(\)]+$/'),

                            ]),
                    ]),

                Section::make('Totals')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('subtotal_amount')
                                    ->label('Subtotal')
                                    ->readOnly()
                                    ->default(0)
                                    ->prefix(fn (Get $get) => self::currencySymbol($get('currency_id'))),

                                TextInput::make('tax_total_amount')
                                    ->label('Tax Total')
                                    ->readOnly()
                                    ->default(0)
                                    ->prefix(fn (Get $get) => self::currencySymbol($get('currency_id'))),

                                TextInput::make('total_amount')
                                    ->label('Total')
                                    ->readOnly()
                                    ->default(0)
                                    ->prefix(fn (Get $get) => self::currencySymbol($get('currency_id'))),
                            ]),
                    ]),

                Section::make('Invoice Information')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('number')
                                    ->label('Invoice Number')
                                    ->placeholder('Auto-generated on save')
                                    ->disabled()
                                    ->dehydrated(false),

                                Select::make('payment_term_id')
                                    ->relationship('paymentTerm', 'name')
                                    ->required()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $paymentTerm = PaymentTerm::find($state);

                                        if (! $paymentTerm) {
                                            return;
                                        }

                                        $issueDate = $get('issue_date')
                                            ? \Illuminate\Support\Facades\Date::parse($get('issue_date'))
                                            : now();

                                        $set('due_date', $issueDate->copy()->addDays($paymentTerm->days)->toDateString());
                                    }),

                                Select::make('currency_id')
                                    ->label('Currency')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        // currency changed → reset cache and recalculate totals
                                        self::$cachedCurrency = null;
                                        self::$cachedCurrencyId = null;
                                        self::calculateTotals($get, $set, $get('items'));
                                    })
                                    ->relationship(
                                        name: 'currency',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query): void {
                                            // Pick a single row per currency code (and optionally symbol)
                                            // This avoids duplicate labels in the dropdown.
                                            $query
                                                ->select('currencies.*')
                                                ->join(
                                                    DB::raw('(SELECT MIN(id) AS id FROM currencies GROUP BY code) x'),
                                                    'x.id',
                                                    '=',
                                                    'currencies.id'
                                                )
                                                ->orderBy('name');
                                        },
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                DatePicker::make('issue_date')
                                    ->default(now())
                                    ->required(),

                                DatePicker::make('due_date')
                                    ->required(),

                                RichEditor::make('notes')
                                    ->columnSpan('full'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('bill_to_line_1')
                                    ->required(),

                                TextInput::make('bill_to_line_2'),

                                TextInput::make('bill_to_postal_code'),

                                TextInput::make('bill_to_city')
                                    ->required(),

                                TextInput::make('bill_to_state'),

                                TextInput::make('bill_to_country')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Contact persons')
                    ->collapsed(true)
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('contactPeople')
                            ->relationship('contactPeople') // morphMany on Invoice
                            ->defaultItems(0)
                            ->minItems(0)
                            ->schema([
                                Select::make('contact_person_id')
                                    ->label('Select from Customer Contacts (optional)')
                                    ->options(function (Get $get): array {
                                        $customerId = $get('../../customer_id');

                                        if (! $customerId) {
                                            return [];
                                        }

                                        return ContactPerson::query()
                                            ->where('contactable_type', Customer::class)
                                            ->where('contactable_id', $customerId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $contactPerson = ContactPerson::find($state);

                                        if ($contactPerson) {
                                            $set('name', $contactPerson->name);
                                            $set('email', $contactPerson->email);
                                            $set('phone', $contactPerson->phone);
                                            $set('position', $contactPerson->position);
                                            $set('is_primary', $contactPerson->is_primary);
                                        }
                                    })
                                    ->disabled(fn (Get $get) => ! $get('../../customer_id'))
                                    ->helperText('Select an existing contact from the customer, or fill in the fields below to create a new invoice-specific contact')
                                    ->columnSpanFull(),

                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),

                                        TextInput::make('phone')
                                            ->tel()
                                            ->telRegex('/^\+?[0-9\s\-\(\)]+$/')
                                            ->maxLength(255),

                                        TextInput::make('position')
                                            ->label('Position / Role')
                                            ->maxLength(255),

                                        Toggle::make('is_primary')
                                            ->label('Primary Contact'),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Add contact person')
                            ->disabled(fn (Get $get) => ! $get('customer_id')),
                    ]),

                Section::make('Invoice Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        Select::make('product_id')
                                            ->label(__('Product'))
                                            ->relationship('product', 'name')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Toggle::make('taxable')
                                                    ->required(),
                                                TextInput::make('unit'),
                                                TextInput::make('price')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0)
                                                    ->prefix('$'),
                                            ])
                                            ->afterStateUpdated(function (Get $get, Set $set, $state): void {

                                                $product = Product::find($state);
                                                $set('name', $product->name);
                                                $set('description', $product->description);
                                                $set('unit_price_amount', $product->price);
                                            }),

                                        TextInput::make('name')
                                            ->required(),

                                        TextInput::make('description')
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make('unit'),

                                        TextInput::make('unit_price_amount')
                                            ->prefix(fn (Get $get) => self::currencySymbol($get('../../currency_id')))
                                            ->label('Unit Price')
                                            ->numeric()
                                            ->step(0.001)
                                            ->default(0)
                                            ->required(),

                                    ]),

                                Grid::make(6)
                                    ->schema([
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->step(0.001)
                                            ->default(1)
                                            ->required(),

                                        TextInput::make('discount_amount')
                                            ->prefix(fn (Get $get) => self::currencySymbol($get('../../currency_id')))
                                            ->label('Discount')
                                            ->numeric()
                                            ->step(0.001)
                                            ->default(0),

                                        TextInput::make('tax_rate')
                                            ->label('Tax Rate (%)')
                                            ->numeric()
                                            ->default(21)
                                            ->disabled(fn (Get $get) => $get('../../customer_tax_id') !== null),

                                        TextInput::make('line_subtotal_amount')
                                            ->prefix(fn (Get $get) => self::currencySymbol($get('../../currency_id')))
                                            ->label('Subtotal')
                                            ->readOnly(),

                                        TextInput::make('tax_amount')
                                            ->prefix(fn (Get $get) => self::currencySymbol($get('../../currency_id')))
                                            ->label('Tax')
                                            ->readOnly(),

                                        TextInput::make('line_total_amount')
                                            ->prefix(fn (Get $get) => self::currencySymbol($get('../../currency_id')))
                                            ->label('Total')
                                            ->readOnly(),
                                    ]),
                            ])
                            ->orderColumn('position')
                            ->defaultItems(1)
                            ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                self::calculateTotals($get, $set, $state);
                            })
                            ->live(debounce: 500)
                            ->disabled(fn ($record) => $record && $record->status !== InvoiceStatus::Draft),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function handleCustomerUpdate(Set $set, Get $get, $state): void
    {
        if (! $state) {
            return;
        }

        $customer = Customer::query()
            ->with(['invoiceAddress', 'companyAddress', 'addresses', 'contactPeople'])
            ->find($state);

        if (! $customer) {
            return;
        }

        $set('customer_name', $customer->name);
        $set('customer_tax_id', $customer->tax_id);
        $set('customer_email', $customer->email);
        $set('customer_phone', $customer->phone);
        $set('currency_id', $customer->currency_id);
        $set('payment_term_id', $customer->payment_term_id);

        if ($customer->payment_term_id) {
            $paymentTerm = PaymentTerm::find($customer->payment_term_id);

            if ($paymentTerm) {
                $dueDate = now()->addDays($paymentTerm->days);
                $set('due_date', $dueDate->toDateString());
            }
        }

        $billingAddress = $customer->invoiceAddress
            ?? $customer->companyAddress
            ?? $customer->addresses->first();

        if ($billingAddress) {
            $set('bill_to_line_1', $billingAddress->address_line_1);
            $set('bill_to_line_2', $billingAddress->address_line_2);
            $set('bill_to_postal_code', $billingAddress->postal_code);
            $set('bill_to_city', $billingAddress->city?->name ?? $billingAddress->city_text);
            $set('bill_to_state', $billingAddress->state?->name ?? $billingAddress->state_text);
            $set('bill_to_country', $billingAddress->country?->name);
        }

        // Get contact persons marked for invoices
        $contactPeopleToAdd = $customer->contactPeople
            ->filter(fn ($contact) => $contact->add_to_invoice === true)
            ->values()
            ->map(fn ($contact) => [
                'name' => $contact->name,
                'email' => $contact->email ?? null,
                'phone' => $contact->phone ?? null,
                'position' => $contact->position ?? null,
                'is_primary' => $contact->is_primary ?? false,
            ])
            ->toArray();

        // Debug: Log what we're trying to set
        \Illuminate\Support\Facades\Log::info('Setting contact people:', [
            'customer_id' => $state,
            'customer_name' => $customer->name,
            'count' => count($contactPeopleToAdd),
            'data' => $contactPeopleToAdd,
        ]);

        // Set the contact people (will clear existing and add new ones)
        $set('contactPeople', $contactPeopleToAdd);
    }

    /**
     * Recompute all line totals and invoice totals in one place.
     */
    protected static function calculateTotals(Get $get, Set $set, ?array $items = null): void
    {
        // Prefer the state passed in from the repeater; fall back to global form state.
        $items ??= $get('items') ?? [];

        $subtotal = 0.0;
        $taxTotal = 0.0;
        $total = 0.0;

        $taxId = $get('customer_tax_id');
        $currencyId = $get('currency_id');
        $currency = self::resolveCurrency($currencyId);
        $precision = $currency?->precision ?? 2;

        foreach ($items as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price_amount'] ?? 0);
            $discount = (float) ($item['discount_amount'] ?? 0);

            $taxRate = $taxId !== null
                ? 0.0
                : (float) ($item['tax_rate'] ?? 0);

            // If VAT exempt, push 0 into the item tax_rate so UI reflects it.
            if ($taxId !== null && ($item['tax_rate'] ?? null) != 0) {
                $set("items.$index.tax_rate", 0);
            }

            $lineSubtotal = ($quantity * $unitPrice) - $discount;
            $lineSubtotal = max(0, $lineSubtotal);

            $lineTax = $lineSubtotal * ($taxRate / 100);
            $lineTotal = $lineSubtotal + $lineTax;

            $lineSubtotal = round($lineSubtotal, $precision);
            $lineTax = round($lineTax, $precision);
            $lineTotal = round($lineTotal, $precision);

            // Write line values back into repeater state.
            $set("items.$index.line_subtotal_amount", $lineSubtotal);
            $set("items.$index.tax_amount", $lineTax);
            $set("items.$index.line_total_amount", $lineTotal);

            // Accumulate invoice totals.
            $subtotal += $lineSubtotal;
            $taxTotal += $lineTax;
            $total += $lineTotal;
        }

        $subtotal = round($subtotal, $precision);
        $taxTotal = round($taxTotal, $precision);
        $total = round($total, $precision);

        $set('subtotal_amount', $subtotal);
        $set('tax_total_amount', $taxTotal);
        $set('total_amount', $total);
    }

    public static function formatMoney($amount, $currencyId): string
    {
        if ($amount === null) {
            return '-';
        }

        $currency = self::resolveCurrency($currencyId);
        $symbol = $currency?->symbol ?? '$';

        // All stored as decimal(8,2)
        $precision = 2;

        return $symbol.number_format((float) $amount, $precision);
    }

    public static function currencySymbol($currencyId): string
    {
        $currency = self::resolveCurrency($currencyId);

        return $currency?->symbol ?? '$';
    }

    /**
     * Function to be used to avoid db look up on each field that uses currency
     */
    public static function resolveCurrency(?int $currencyId): ?Currency
    {
        if (! $currencyId) {
            return null;
        }

        if (self::$cachedCurrency !== null && self::$cachedCurrencyId === $currencyId) {
            return self::$cachedCurrency;
        }

        $currency = Currency::find($currencyId);

        self::$cachedCurrencyId = $currencyId;
        self::$cachedCurrency = $currency;

        return $currency;
    }
}
