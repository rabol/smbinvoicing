<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CustomerType: string implements HasLabel
{
    case None = 'none';
    case Person = 'person';
    case Company = 'company';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => __('none'),
            self::Person => __('person'),
            self::Company => __('company'),
        };
    }
}
