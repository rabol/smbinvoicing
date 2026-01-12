<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Traits\HasResourceDocumentation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    use HasResourceDocumentation;

    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getHelpAction(),
            CreateAction::make(),
        ];
    }
}
