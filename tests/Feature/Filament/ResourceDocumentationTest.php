<?php

declare(strict_types=1);

use App\Filament\Resources\Cities\CityResource;
use App\Filament\Resources\Cities\Pages\ListCities;
use App\Models\User;
use App\Services\ResourceDocumentationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('can display help button on cities list page', function (): void {
    Livewire::test(ListCities::class)
        ->assertActionExists('help');
});

it('can load cities documentation', function (): void {
    $service = app(ResourceDocumentationService::class);

    $documentation = $service->loadDocumentation(CityResource::class);

    expect($documentation)
        ->not->toBeNull()
        ->toContain('Cities Resource Documentation');
});

it('converts resource class name to correct filename', function (): void {
    $service = app(ResourceDocumentationService::class);

    $path = $service->getDocumentationPath(CityResource::class);

    expect($path)
        ->toEndWith('cities.md')
        ->and($service->exists(CityResource::class))
        ->toBeTrue();
});

it('returns null for non-existent documentation', function (): void {
    $service = app(ResourceDocumentationService::class);

    $documentation = $service->loadDocumentation('NonExistentResource');

    expect($documentation)->toBeNull();
});

it('parses markdown to html', function (): void {
    $service = app(ResourceDocumentationService::class);

    $markdown = '# Hello World';
    $html = $service->parseMarkdown($markdown);

    expect($html)
        ->toContain('<h1')
        ->toContain('Hello World')
        ->toContain('</h1>');
});

it('can display help button on countries list page', function (): void {
    Livewire::test(\App\Filament\Resources\Countries\Pages\ListCountries::class)
        ->assertActionExists('help');
});

it('can load countries documentation', function (): void {
    $service = app(ResourceDocumentationService::class);

    $documentation = $service->loadDocumentation(\App\Filament\Resources\Countries\CountryResource::class);

    expect($documentation)
        ->not->toBeNull()
        ->toContain('Countries Resource Documentation');
});

it('can display help button on states list page', function (): void {
    Livewire::test(\App\Filament\Resources\States\Pages\ListStates::class)
        ->assertActionExists('help');
});

it('can load states documentation', function (): void {
    $service = app(ResourceDocumentationService::class);

    $documentation = $service->loadDocumentation(\App\Filament\Resources\States\StateResource::class);

    expect($documentation)
        ->not->toBeNull()
        ->toContain('States Resource Documentation');
});
