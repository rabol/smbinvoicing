<?php

declare(strict_types=1);

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a product using factory', function () {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->not->toBeNull();
});

it('has fillable attributes', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => 99.99,
        'taxable' => true,
        'unit' => 'piece',
    ]);

    expect($product->name)->toBe('Test Product')
        ->and($product->description)->toBe('Test Description')
        ->and($product->price)->toBe('99.990')
        ->and($product->taxable)->toBeTrue()
        ->and($product->unit)->toBe('piece');
});

it('casts taxable as boolean', function () {
    $product = Product::factory()->create(['taxable' => 1]);

    expect($product->taxable)->toBeTrue();
});

it('casts price as decimal', function () {
    $product = Product::factory()->create(['price' => 123.456]);

    expect($product->price)->toBeString()
        ->and((float) $product->price)->toBeGreaterThan(123.0);
});
