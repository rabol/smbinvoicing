<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NumberSequenceResetFrequency;
use App\Models\NumberSequence;
use DateTimeInterface;

class NumberSequenceService
{
    /**
     * The sequence constructor.
     */
    public function __construct(protected NumberSequence $numberSequence, protected DateTimeInterface $date)
    {
        //
    }

    /**
     * Get the sequence number.
     */
    public function getNumber(bool $increment = false): string
    {
        $result = strtr(
            str($this->getPattern())
                ->replaceMatches(
                    '/\{number:(\d+)\}/',
                    fn ($matches) => str_pad(
                        (string) $this->getOrdinalNumber(),
                        (int) $matches[1],
                        '0',
                        STR_PAD_LEFT
                    )
                )
                ->toString(),
            [
                '{number}' => $this->getOrdinalNumber(),
                '{day}' => $this->date->format('d'),
                '{DD}' => $this->date->format('d'),
                '{month}' => $this->date->format('m'),
                '{MM}' => $this->date->format('m'),
                '{year}' => $this->date->format('Y'),
                '{YYYY}' => $this->date->format('Y'),
                '{day_short}' => $this->date->format('j'),
                '{D}' => $this->date->format('j'),
                '{month_short}' => $this->date->format('n'),
                '{M}' => $this->date->format('n'),
                '{year_short}' => $this->date->format('y'),
                '{YY}' => $this->date->format('y'),
            ]
        );

        if ($increment) {
            $this->increment();
        }

        return $result;
    }

    /**
     * Get the ordinal number of sequence.
     */
    public function getOrdinalNumber(bool $increment = false): int
    {
        $result = $this->numberSequence->ordinal_number;

        if ($increment) {
            $this->increment();
        }

        return $result;
    }

    /**
     * Get the pattern of sequence.
     */
    public function getPattern(): string
    {
        return $this->numberSequence->pattern;
    }

    /**
     * Increment ordinal number of period.
     */
    public function increment(): void
    {
        $this->numberSequence->increment('ordinal_number');
    }

    /**
     * Decide whether ordinal number needs to be reset yearly.
     */
    public function needsYearlyReset(): bool
    {
        return in_array(NumberSequenceResetFrequency::tryFrom($this->numberSequence->reset_frequency ?? ''), [
            NumberSequenceResetFrequency::Yearly,
            NumberSequenceResetFrequency::Monthly,
            NumberSequenceResetFrequency::Daily,
        ]);
    }

    /**
     * Decide whether the ordinal number needs to be reset monthly.
     */
    public function needsMonthlyReset(): bool
    {
        return in_array(NumberSequenceResetFrequency::tryFrom($this->numberSequence->reset_frequency ?? ''), [
            NumberSequenceResetFrequency::Monthly,
            NumberSequenceResetFrequency::Daily,
        ]);
    }

    /**
     * Decide whether ordinal number needs to be reset daily.
     */
    public function needsDailyReset(): bool
    {
        return in_array(NumberSequenceResetFrequency::tryFrom($this->numberSequence->reset_frequency ?? ''), [
            NumberSequenceResetFrequency::Daily,
        ]);
    }
}
