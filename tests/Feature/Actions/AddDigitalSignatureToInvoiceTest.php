<?php

declare(strict_types=1);

use App\Actions\AddDigitalSignatureToInvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('invoices');
    config(['services.authorizedoc.api_key' => 'test-api-key']);
    config(['services.authorizedoc.endpoint' => 'https://api.example.com/sign']);
});

test('can create instance using make method', function () {
    $action = AddDigitalSignatureToInvoice::make();

    expect($action)->toBeInstanceOf(AddDigitalSignatureToInvoice::class);
});

test('throws exception when file does not exist', function () {
    $action = AddDigitalSignatureToInvoice::make();

    $action->handle('non-existent.pdf');
})->throws(\InvalidArgumentException::class, 'File does not exist on public disk: non-existent.pdf');

test('throws exception when file is not a pdf', function () {
    Storage::disk('invoices')->put('document.txt', 'test content');

    $action = AddDigitalSignatureToInvoice::make();

    $action->handle('document.txt');
})->throws(\InvalidArgumentException::class, 'File must be a PDF. Given: .txt');

test('throws exception when api key is missing', function () {
    config(['services.authorizedoc.api_key' => '']);
    Storage::disk('invoices')->put('invoice.pdf', '%PDF-1.4 test content');

    $action = AddDigitalSignatureToInvoice::make();

    $action->handle('invoice.pdf');
})->throws(\InvalidArgumentException::class, 'Missing authorizedoc token');

test('throws exception when api response does not contain base64 data', function () {
    Storage::disk('invoices')->put('invoice.pdf', '%PDF-1.4 test content');

    Http::fake([
        '*' => Http::response(['data' => []], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();

    $action->handle('invoice.pdf');
})->throws(\RuntimeException::class, 'Sign service did not return base64 PDF at key: data');

test('throws exception when base64 decode fails', function () {
    Storage::disk('invoices')->put('invoice.pdf', '%PDF-1.4 test content');

    Http::fake([
        '*' => Http::response(['data' => ['base64' => 'invalid-base64!@#$%']], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();

    $action->handle('invoice.pdf');
})->throws(\RuntimeException::class, 'Failed to base64-decode signed PDF');

test('throws exception when decoded data is not a pdf', function () {
    Storage::disk('invoices')->put('invoice.pdf', '%PDF-1.4 test content');

    $invalidPdfBase64 = base64_encode('This is not a PDF');

    Http::fake([
        '*' => Http::response(['data' => ['base64' => $invalidPdfBase64]], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();

    $action->handle('invoice.pdf');
})->throws(\RuntimeException::class, 'Decoded signed data does not look like a PDF');

test('successfully signs pdf and overwrites original', function () {
    $originalPdf = '%PDF-1.4 original content';
    $signedPdf = '%PDF-1.5 signed content with signature';

    Storage::disk('invoices')->put('invoice.pdf', $originalPdf);

    Http::fake([
        '*' => Http::response([
            'data' => [
                'base64' => base64_encode($signedPdf),
            ],
        ], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $result = $action->handle('invoice.pdf');

    expect($result)->toBe('invoice.pdf');
    expect(Storage::disk('invoices')->get('invoice.pdf'))->toBe($signedPdf);
});

test('sends correct payload to api', function () {
    $originalPdf = '%PDF-1.4 test content';
    $signedPdf = '%PDF-1.5 signed content';

    Storage::disk('invoices')->put('invoice.pdf', $originalPdf);

    Http::fake([
        '*' => Http::response([
            'data' => [
                'base64' => base64_encode($signedPdf),
            ],
        ], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $action->handle('invoice.pdf', [
        'name' => 'Test Invoice',
        'customData' => [['key' => 'value']],
    ]);

    Http::assertSent(function ($request) use ($originalPdf) {
        $body = $request->data();

        return $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('Content-Type', 'application/json')
            && $request->url() === 'https://api.example.com/sign'
            && $body['name'] === 'Test Invoice'
            && $body['customData'] === [['key' => 'value']]
            && $body['document'] === base64_encode($originalPdf);
    });
});

test('uses custom endpoint and token when provided', function () {
    $originalPdf = '%PDF-1.4 test content';
    $signedPdf = '%PDF-1.5 signed content';

    Storage::disk('invoices')->put('invoice.pdf', $originalPdf);

    Http::fake([
        'https://custom-endpoint.com/sign' => Http::response([
            'data' => [
                'base64' => base64_encode($signedPdf),
            ],
        ], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $action->handle('invoice.pdf', [
        'endpoint' => 'https://custom-endpoint.com/sign',
        'token' => 'custom-token',
    ]);

    Http::assertSent(function ($request) {
        return $request->hasHeader('Authorization', 'Bearer custom-token')
            && $request->url() === 'https://custom-endpoint.com/sign';
    });
});

test('uses custom timeout when provided', function () {
    $originalPdf = '%PDF-1.4 test content';
    $signedPdf = '%PDF-1.5 signed content';

    Storage::disk('invoices')->put('invoice.pdf', $originalPdf);

    Http::fake([
        '*' => Http::response([
            'data' => [
                'base64' => base64_encode($signedPdf),
            ],
        ], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $action->handle('invoice.pdf', ['timeout' => 120]);

    Http::assertSent(function ($request) {
        return true;
    });
});

test('returns null when api request fails', function () {
    Storage::disk('invoices')->put('invoice.pdf', '%PDF-1.4 test content');

    Http::fake([
        '*' => Http::response([], 500),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $result = $action->handle('invoice.pdf');

    expect($result)->toBeNull();
});

test('handles pdf in subdirectory correctly', function () {
    $originalPdf = '%PDF-1.4 original content';
    $signedPdf = '%PDF-1.5 signed content';

    Storage::disk('invoices')->put('2026/01/invoice.pdf', $originalPdf);

    Http::fake([
        '*' => Http::response([
            'data' => [
                'base64' => base64_encode($signedPdf),
            ],
        ], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $result = $action->handle('2026/01/invoice.pdf');

    expect($result)->toBe('2026/01/invoice.pdf');
    expect(Storage::disk('invoices')->get('2026/01/invoice.pdf'))->toBe($signedPdf);
});

test('generates unique temporary filenames', function () {
    $originalPdf = '%PDF-1.4 test content';
    $signedPdf = '%PDF-1.5 signed content';

    Storage::disk('invoices')->put('invoice.pdf', $originalPdf);

    Http::fake([
        '*' => Http::response([
            'data' => [
                'base64' => base64_encode($signedPdf),
            ],
        ], 200),
    ]);

    $action = AddDigitalSignatureToInvoice::make();
    $action->handle('invoice.pdf');

    $files = Storage::disk('invoices')->files();

    expect($files)->toHaveCount(1);
    expect($files[0])->toBe('invoice.pdf');
});
