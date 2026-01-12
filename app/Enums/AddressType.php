<?php

declare(strict_types=1);

namespace App\Enums;

enum AddressType: string
{
    case Company = 'company';
    case Invoice = 'invoice';
    case Delivery = 'delivery';
}
