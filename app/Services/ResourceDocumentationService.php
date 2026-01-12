<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class ResourceDocumentationService
{
    public function __construct(
        protected MarkdownRenderer $markdown
    ) {}

    public function getDocumentationPath(string $resourceName): string
    {
        $filename = $this->getFilename($resourceName);

        return storage_path("app/docs/resources/{$filename}");
    }

    public function loadDocumentation(string $resourceName): ?string
    {
        $path = $this->getDocumentationPath($resourceName);

        if (! file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);

        if ($content === false || empty(trim($content))) {
            return null;
        }

        return $this->parseMarkdown($content);
    }

    public function parseMarkdown(string $content): string
    {
        return $this->markdown
            ->highlightCode()
            ->highlightTheme('github-dark')
            ->toHtml($content);
    }

    public function exists(string $resourceName): bool
    {
        $path = $this->getDocumentationPath($resourceName);

        return file_exists($path) && is_readable($path);
    }

    protected function getFilename(string $resourceName): string
    {
        // Convert "CityResource" or "App\Filament\Resources\Cities\CityResource" to "cities"
        $name = class_basename($resourceName);
        $name = str_replace('Resource', '', $name);

        // Pluralize the name (City -> cities, Country -> countries)
        $name = Str::plural($name);

        return Str::lower($name).'.md';
    }
}
