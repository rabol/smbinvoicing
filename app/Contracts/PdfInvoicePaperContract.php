<?php

declare(strict_types=1);

namespace App\Contracts;

interface PdfInvoicePaperContract
{
    public function getSize(): string;

    public function getOrientation(): string;
}
