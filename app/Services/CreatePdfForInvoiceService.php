<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PdfInvoicePaperContract;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Nnjeim\World\Models\Currency;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CreatePdfForInvoiceService
{
    public const int TABLE_COLUMNS = 4;

    public string $template = 'default';

    public string $filename = '';

    public string $logo = '';

    public int $table_columns = 4;

    public string $output = '';

    protected ?PdfInvoicePaperContract $paperOptions = null;

    protected array $domPdfOptions = [];

    public string $disk = 'invoices';

    private string $dateFormat = 'Y-m-d';

    public function __construct(public Invoice $invoice)
    {
        // DomPDF options
        $this->domPdfOptions = array_merge(resolve('dompdf.options'), ['enable_php' => true]);
        $this->table_columns = static::TABLE_COLUMNS;
    }

    public function setPaperOptions(PdfInvoicePaperContract $paperOptions): static
    {
        $this->paperOptions = $paperOptions;

        return $this;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function setTemplate(string $template = 'default'): static
    {
        $this->template = $template;

        return $this;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = sprintf('%s.pdf', $filename);

        return $this;
    }

    protected function getDefaultFilename(): string
    {
        return Str::slug($this->invoice->number).'.pdf';
    }

    /**
     * @throws Exception
     */
    public function render(): static
    {
        $template = sprintf('invoices.%s', $this->template);
        $view = View::make($template, ['pdfCreator' => $this]);

        $html = mb_convert_encoding($view->render(), 'HTML-ENTITIES', 'UTF-8');

        $pdf = PDF::setOptions($this->domPdfOptions)
            ->setPaper($this->paperOptions->getSize(), $this->paperOptions->getOrientation())
            ->loadHtml($html);

        $this->output = $pdf->output();

        return $this;
    }

    /**
     * @throws Exception
     */
    public function stream(): Response
    {
        $this->render();

        return new Response($this->output, ResponseAlias::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this->filename.'"',
        ]);
    }

    /**
     * @throws Exception
     */
    public function download(): Response
    {
        $this->render();

        return new Response($this->output, ResponseAlias::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'"',
            'Content-Length' => strlen($this->output),
        ]);
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function save(string $disk = ''): bool
    {
        if ($disk !== '') {
            $this->disk = $disk;
        }

        $this->render();

        if ($this->filename == '') {
            $this->filename = $this->getDefaultFilename();
        }

        return Storage::disk($this->disk)->put($this->filename, $this->output);
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->filename);
    }

    public static function make(Invoice $invoice): static
    {
        return new static($invoice);
    }

    public function setDateFormat(string $dateFormat): static
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function formatCurrency(float $amount, Currency $currency): string
    {
        $value = number_format(
            $amount,
            $currency->precision,
            $currency->decimal_mark,
            $currency->thousands_separator
        );

        $currencyFormat = '{SYMBOL} {VALUE}';
        if ($currency->symbol_first != 1) {
            $currencyFormat = '{VALUE} {SYMBOL}';
        }

        return strtr($currencyFormat, [
            '{VALUE}' => $value,
            '{SYMBOL}' => $currency->symbol,
            '{CODE}' => $currency->code,
        ]);
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }
}
