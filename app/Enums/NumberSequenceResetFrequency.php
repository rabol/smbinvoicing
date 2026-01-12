<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NumberSequenceResetFrequency: string implements HasLabel
{
    case Yearly = 'yearly';
    case Monthly = 'monthly';
    case Daily = 'daily';
    case Never = 'never';

    public function getLabel(): string
    {
        return match ($this) {
            self::Yearly => __('yearly'),
            self::Monthly => __('monthly'),
            self::Daily => __('daily'),
            self::Never => __('Never'),
        };
    }
}
