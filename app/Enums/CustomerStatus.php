<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CustomerStatus: string implements HasLabel
{
    case None = 'none';
    case Active = 'active';
    case Blocked = 'blocked';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => __('None'),
            self::Active => __('active'),
            self::Blocked => __('blocked'),
            self::Archived => __('archived'),
        };
    }
}
