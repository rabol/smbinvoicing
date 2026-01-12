<?php

declare(strict_types=1);

use App\Mail\InvoiceMail;

it('can be instantiated with logo', function () {
    $logoPath = '/path/to/logo.png';

    $mail = new InvoiceMail(
        subject: 'Test Invoice',
        html: '<html><body>Test</body></html>',
        pdfPath: '/tmp/test.pdf',
        filename: 'invoice-123.pdf',
        logoPath: $logoPath
    );

    expect($mail)->toBeInstanceOf(InvoiceMail::class)
        ->and($mail->logoPath)->toBe($logoPath);
});

it('can be instantiated without logo', function () {
    $mail = new InvoiceMail(
        subject: 'Test Invoice',
        html: '<html><body>Test</body></html>',
        pdfPath: '/tmp/test.pdf',
        filename: 'invoice-123.pdf',
        logoPath: null
    );

    expect($mail)->toBeInstanceOf(InvoiceMail::class)
        ->and($mail->logoPath)->toBeNull();
});

it('can be instantiated with default null logo when parameter is omitted', function () {
    $mail = new InvoiceMail(
        subject: 'Test Invoice',
        html: '<html><body>Test</body></html>',
        pdfPath: '/tmp/test.pdf',
        filename: 'invoice-123.pdf'
    );

    expect($mail)->toBeInstanceOf(InvoiceMail::class)
        ->and($mail->logoPath)->toBeNull();
});
