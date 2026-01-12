<?php

declare(strict_types=1);

use App\Classes\PdfInvoiceA4Paper;
use App\Contracts\PdfInvoicePaperContract;

test('implements PdfInvoicePaperContract', function () {
    $paper = new PdfInvoiceA4Paper;

    expect($paper)->toBeInstanceOf(PdfInvoicePaperContract::class);
});

test('returns A4 as paper size', function () {
    $paper = new PdfInvoiceA4Paper;

    expect($paper->getSize())->toBe('A4');
});

test('returns portrait as orientation', function () {
    $paper = new PdfInvoiceA4Paper;

    expect($paper->getOrientation())->toBe('portrait');
});
