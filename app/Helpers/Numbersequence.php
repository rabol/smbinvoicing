<?php

declare(strict_types=1);

use App\Models\Invoice;
use App\Models\NumberSequence;
use App\Services\NumberSequenceService;

if (! function_exists('getInvoiceSequence')) {

    function getInvoiceSequence(): ?NumberSequenceService
    {
        return getSequence('invoice', now());
    }
}

if (! function_exists('getProductSequence')) {

    function getProductSequence(): ?NumberSequenceService
    {
        return getSequence('product', now());
    }
}

if (! function_exists('getSequence')) {

    function getSequence(string $type, ?DateTimeInterface $date = null): ?NumberSequenceService
    {
        $numberSequence = NumberSequence::query()->where('type', $type)->first();
        if ($numberSequence) {
            /**
             * Create a sequence by passing sequence type and date (e.g. issue date of invoice).
             */
            if (is_null($date)) {
                $date = now();
            }

            return new NumberSequenceService($numberSequence, $date);
        }

        return null;
    }
}

if (! function_exists('isValidSequenceNumber')) {
    function isValidSequenceNumber(string $number, NumberSequenceService $sequence): bool
    {
        $pattern = $sequence->getPattern();

        $replacements = [
            '{day}' => '(\d{2})',
            '{DD}' => '(\d{2})',
            '{month}' => '(\d{2})',
            '{MM}' => '(\d{2})',
            '{year}' => '(\d{4})',
            '{YYYY}' => '(\d{4})',
            '{day_short}' => '(\d{1,2})',
            '{D}' => '(\d{1,2})',
            '{month_short}' => '(\d{1,2})',
            '{M}' => '(\d{1,2})',
            '{year_short}' => '(\d{2})',
            '{YY}' => '(\d{2})',
        ];

        $captureGroupIndex = 0;
        $ordinalCaptureGroup = null;

        // Process the pattern and count groups correctly
        $regexPattern = preg_replace_callback('/\{(number(?::(\d+))?|day|DD|month|MM|year|YYYY|day_short|D|month_short|M|year_short|YY)\}/', function ($matches) use (&$ordinalCaptureGroup, &$captureGroupIndex, $replacements) {
            $captureGroupIndex++;

            $placeholder = $matches[1];

            if (str_starts_with($placeholder, 'number')) {
                if ($ordinalCaptureGroup === null) {
                    $ordinalCaptureGroup = $captureGroupIndex;
                }

                if (isset($matches[2])) {
                    return "(\d{{$matches[2]}})";
                }

                return '(\d+)';
            }

            return $replacements['{'.$placeholder.'}'];
        }, $pattern);

        if ($ordinalCaptureGroup === null) {
            throw new \RuntimeException('No {number} placeholder found in pattern.');
        }

        if (! preg_match("/^{$regexPattern}$/", $number, $matches)) {
            return false;
        }

        if (! isset($matches[$ordinalCaptureGroup])) {
            throw new \RuntimeException("Expected ordinal capture group {$ordinalCaptureGroup} does not exist in match.");
        }

        $providedOrdinal = (int) $matches[$ordinalCaptureGroup];
        $expectedOrdinal = $sequence->getOrdinalNumber();

        return $providedOrdinal >= ($expectedOrdinal - 5) && $providedOrdinal <= $expectedOrdinal;
    }
}
