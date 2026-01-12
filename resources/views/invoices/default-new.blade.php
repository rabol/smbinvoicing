@php
    use App\Classes\AddressFormatter;
    use App\Models\Customer;
    use App\Models\Invoice;
@endphp
        <!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ __('invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css">
        html, body {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            background-color: #fff;
            color: #212529;
            line-height: 1.1;
        }

        body {
            margin: 36pt;
        }

        h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        p {
            margin: 0 0 1rem 0;
        }

        strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            vertical-align: top;
        }

        .table {
            margin-bottom: 1rem;
        }

        .table-items td {
            border-top: 1px solid #dee2e6;
        }

        .table-items thead th {
            border-bottom: 2px solid #dee2e6;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pl-0 {
            padding-left: 0 !important;
        }

        .pr-0 {
            padding-right: 0 !important;
        }

        .px-0 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .border-0 {
            border: none !important;
        }

        .cool-gray {
            color: #6B7280;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
            text-align: left;
            margin-bottom: 0.5rem;
        }

        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>

<body>
@php
    $columnCount = 7;
    /** @var Invoice $invoice */
    $invoice = $pdfCreator->invoice;
    /** @var Customer $customer */
    $customer = $pdfCreator->invoice->customer;

    $hasUnits = $invoice->has_item_units;
    $hasDiscount = $invoice->has_item_discount;
    $hasTax = $invoice->has_item_tax;
    $columnCount = 4 + ($hasUnits ? 1 : 0) + ($hasDiscount ? 1 : 0) + ($hasTax ? 2 : 0);
    $company = app(\App\Settings\CompanySettings::class);
@endphp

<table class="table">
    <tr>
        <td class="border-0 pl-0" width="40%">
            @if($pdfCreator->logo)
                <img src="{{ $pdfCreator->getLogo() }}" alt="logo" height="100">
            @endif
        </td>
        <td class="border-0 pl-0">
            {{-- Company info --}}
            <table style="border-collapse: collapse;">
                <tr>
                    <td style="padding: 2px;">{{ $company->name }}</td>
                    <td style="padding: 2px;">{{ $company->address_line_1 }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">{{ $company->email }}</td>
                    <td style="padding: 2px;">{{ $company->postal_code }}, {{ $company->city }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">{{ $company->url }}</td>
                    <td style="padding: 2px;">{{ $company->state }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">{{ $company->vat_id }}</td>
                    <td style="padding: 2px;">{{ $company->country }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Prepare the address --}}

@php
    $countryIso2 = Nnjeim\World\Models\Country::query()->where('name', $invoice->bill_to_country)->first()->iso2;
@endphp
{{-- Invoice Header --}}
<table class="table mt-5">
    <tr>
        <td class="border-0 pl-0" width="60%">

            {!! nl2br(AddressFormatter::format([
            'company' => '<strong>' . e($invoice->customer_name) . '</strong>',
            'street' => $invoice->bill_to_line_1,
            'street_2' => $invoice->bill_to_line_2,
            'postal_code' => $invoice->bill_to_postal_code,
            'city' => $invoice->bill_to_city,
            'state' => $invoice->bill_to_state,
            'province' => '', //$invoice->state->iso2 ?? '',
            'country' => $invoice->bill_to_country,
        ], $countryIso2 ?? 'US')) !!}
            @if($invoice->customer_tax_id)
                <br/><br/>{{__('Vat-Id')}} : {{$invoice->customer_tax_id}}
            @endif
        </td>
        <td class="border-0 pl-0">
            <h4 class="text-uppercase"><strong>{{ __('invoice') }}</strong></h4>
            <p>{{ __('invoices/default.serial') }} : <strong>{{ $invoice->number }}</strong></p>
            <p>{{ __('invoices/default.date') }} :
                <strong>{{ $invoice->issue_date->format($pdfCreator->getDateFormat()) }}</strong></p>
            <p>{{ __('invoices/default.pay_until') }}
                : {{ $invoice->due_date->format($pdfCreator->getDateFormat()) }}</p>
        </td>
    </tr>
</table>
<br/>
{{-- Table: Items --}}
<table class="table table-items">
    <colgroup>
        {{-- name / description --}}
        @if($columnCount == 8)
            <col style="width:25%">
        @else
            <col style="width:35%">
        @endif
        @if($hasUnits)
        <col style="width:10%">
        @endif
        <col style="width:10%">
        <col style="width:15%">
        @if($hasDiscount)
        <col style="width:10%">
        @endif
        @if($hasTax)
        <col style="width:10%">
        <col style="width:5%">
        @endif
        <col style="width:10%">
    </colgroup>
    <thead>
    <tr>
        <th class="text-left border-0 pl-0">{{ __('invoices/default.description') }}</th>
        @if($hasUnits)
        <th class="text-right border-0">{{ __('invoices/default.units') }}</th>
        @endif
        <th class="text-right border-0">{{ __('invoices/default.quantity') }}</th>
        <th class="text-right border-0">{{ __('invoices/default.price') }}</th>
        @if($hasDiscount)
        <th class="text-right border-0">{{ __('invoices/default.discount') }}</th>
        @endif
        @if($hasTax)
        <th class="text-right border-0">{{ __('invoices/default.tax') }}</th>
        <th class="text-right border-0">{{ __('invoices/default.tax_rate') }}</th>
        @endif
        <th class="text-right border-0 pr-0">{{ __('invoices/default.sub_total') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->items as $item)
        <tr>
            <td class="pl-0">
                {{ $item->name }}
                @if($item->description)
                    <p class="cool-gray">{{ $item->description }}</p>
                @endif
            </td>

            @if($hasUnits)
            <td class="text-right">{{ $item->unit }}</td>
            @endif
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ $pdfCreator->formatCurrency($item->unit_price_amount, $invoice->currency) }}</td>
            @if($hasDiscount)
            <td class="text-right">{{ $pdfCreator->formatCurrency($item->discount_amount,$invoice->currency) }}</td>
            @endif
            @if($hasTax)
            <td class="text-right">{{ $pdfCreator->formatCurrency($item->tax_amount,$invoice->currency) }}</td>
            <td class="text-right">{{ number_format($item->tax_rate,2) }}</td>
            @endif
            <td class="text-right pr-0">{{ $pdfCreator->formatCurrency($item->line_subtotal_amount,$invoice->currency) }}</td>
        </tr>
    @endforeach

    {{-- Summary --}}
    <tr>
        <td colspan="{{ $columnCount - 2 }}"></td>
        <td class="text-right pl-0">{{ __('invoices/default.total_discount') }}</td>
        <td class="text-right pr-0">{{ $pdfCreator->formatCurrency($invoice->total_discount_amount, $invoice->currency) }}</td>
    </tr>

    <tr>
        <td colspan="{{ $columnCount - 2 }}"></td>
        <td class="text-right pl-0">{{ __('invoices/default.taxable_amount') }}</td>
        <td class="text-right pr-0">{{ $pdfCreator->formatCurrency($invoice->subtotal_amount, $invoice->currency) }}</td>
    </tr>

    <tr>
        <td colspan="{{ $columnCount - 2 }}"></td>
        <td class="text-right pl-0">{{ __('invoices/default.total_taxes') }}</td>
        <td class="text-right pr-0">{{ $pdfCreator->formatCurrency($invoice->tax_total_amount, $invoice->currency) }}</td>
    </tr>

    <tr>
        <td colspan="{{ $columnCount - 2 }}"></td>
        <td class="text-right pl-0">{{ __('invoices/default.shipping') }}</td>
        <td class="text-right pr-0">{{ $pdfCreator->formatCurrency($invoice->total_shipping_amount, $invoice->currency) }}</td>
    </tr>

    <tr>
        <td colspan="{{ $columnCount - 2 }}"></td>
        <td class="text-right pl-0">{{ __('invoices/default.total_amount') }}</td>
        <td class="text-right pr-0 total-amount">{{ $pdfCreator->formatCurrency($invoice->total_amount, $invoice->currency) }}</td>
    </tr>
    </tbody>
</table>

{{-- Notes --}}
@if($invoice->notes)
    <p>{{ __('invoices/default.notes') }}: {!! nl2br($invoice->notes) !!}</p>
@endif

{{-- Page Numbers and bank details --}}
<script type="text/php">
    if (isset($pdf)) {

        // Page numbers
        if ($PAGE_COUNT > 1) {
            $text = "{{ __('invoices/default.page') }} {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width);
            $y = $pdf->get_height() - 35;

            $pdf->page_text($x, $y, $text, $font, $size);
        }

        // Only on the last page
        if ($PAGE_NUM === $PAGE_COUNT) {

            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $size = 7.5;

            $y = $pdf->get_height() - 40;

            $pdf->text(36, $y, "{{ __('Bank name') }}: {{ $company->bank_name }}", $font, $size);
            $pdf->text(300, $y, "{{ __('Bank account') }}: {{ $company->bank_account }}", $font, $size);

            $pdf->text(36, $y + 12, "{{ __('IBAN') }}: {{ $company->iban }}", $font, $size);
            $pdf->text(300, $y + 12, "{{ __('BIC') }}: {{ $company->bic }}", $font, $size);
        }
    }
</script>
</body>
</html>