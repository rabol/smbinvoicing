<?php

declare(strict_types=1);

namespace App\Classes;

use App\Contracts\PdfInvoicePaperContract;

class PdfInvoiceA4Paper implements PdfInvoicePaperContract
{
    public function getSize(): string
    {
        return 'A4';
    }

    public function getOrientation(): string
    {
        return 'portrait';
    }
}
