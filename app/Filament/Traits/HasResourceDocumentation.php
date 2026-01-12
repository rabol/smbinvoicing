<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use App\Services\ResourceDocumentationService;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;

trait HasResourceDocumentation
{
    protected function getHelpAction(): Action
    {
        $service = app(ResourceDocumentationService::class);
        $resourceClass = static::$resource;

        $documentation = $service->loadDocumentation($resourceClass);

        if ($documentation === null) {
            return Action::make('help')
                ->label('Help')
                ->modalHeading('Documentation Not Available')
                ->modalDescription('Documentation for this resource has not been created yet.')
                ->modalCancelActionLabel('Close')
                ->modalSubmitAction(false);
        }

        return Action::make('help')
            ->label('Help')
            ->modalHeading($this->getDocumentationTitle())
            ->modalContent(new HtmlString(
                '<div class="prose prose-base dark:prose-invert max-w-none">'.
                '<style>'.
                '.prose h1 { font-size: 1.875rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; }'.
                '.prose h2 { font-size: 1.5rem; font-weight: 600; margin-top: 2rem; margin-bottom: 0.75rem; }'.
                '.prose h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.5rem; }'.
                '.prose p { margin-bottom: 1rem; line-height: 1.75; }'.
                '.prose ul, .prose ol { margin-bottom: 1rem; padding-left: 1.5rem; }'.
                '.prose li { margin-bottom: 0.5rem; }'.
                '.prose code { background-color: rgba(110, 118, 129, 0.2); padding: 0.2em 0.4em; border-radius: 0.25rem; font-size: 0.875em; }'.
                '.prose strong { font-weight: 600; }'.
                '.prose a { color: rgb(96, 165, 250); text-decoration: underline; }'.
                '</style>'.
                $documentation.
                '</div>'
            ))
            ->modalWidth('5xl')
            ->modalCancelActionLabel('Close')
            ->modalSubmitAction(false)
            ->slideOver();
    }

    protected function getDocumentationTitle(): string
    {
        $resourceClass = static::$resource;
        $name = class_basename($resourceClass);
        $name = str_replace('Resource', '', $name);

        return "{$name} Documentation";
    }
}
