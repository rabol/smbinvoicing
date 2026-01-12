<?php

declare(strict_types=1);

namespace App\Filament\Resources\States\Pages;

use App\Filament\Resources\States\StateResource;
use App\Filament\Traits\HasResourceDocumentation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStates extends ListRecords
{
    use HasResourceDocumentation;

    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getHelpAction(),
            CreateAction::make(),
        ];
    }
}
