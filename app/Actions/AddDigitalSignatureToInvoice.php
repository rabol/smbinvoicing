<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class AddDigitalSignatureToInvoice
{
    public static function make(): AddDigitalSignatureToInvoice
    {
        return new AddDigitalSignatureToInvoice;
    }

    /**
     * Read a PDF from the public disk, send it to the sign service, and overwrite the same file
     * with the signed version (ONLY if signing succeeds and the returned data looks valid).
     *
     * @param  string  $path  Path on the public disk, e.g. "invoices/inv-20260104-000001.pdf"
     * @param  array{
     *   name?: string,
     *   integrationId?: string,
     *   customData?: array,
     *   endpoint?: string,
     *   token?: string,
     *   timeout?: int
     * }  $options
     * @return string The same $path that was overwritten.
     */
    public function handle(string $path, array $options = []): ?string
    {
        $disk = Storage::disk('invoices');

        if (! $disk->exists($path)) {
            throw new \InvalidArgumentException("File does not exist on public disk: {$path}");
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            throw new \InvalidArgumentException("File must be a PDF. Given: .{$ext}");
        }

        // Read + base64 encode original PDF
        $rawPdfData = base64_encode((string) $disk->get($path));

        $payload = [
            'name' => $options['name'] ?? ('Document: '.Str::uuid()->toString()),
            // 'integrationId' => $options['integrationId'] ?? config('services.authorizedoc.integration_id'),
            'customData' => $options['customData'] ?? [[]],
            'document' => $rawPdfData,
        ];

        $token = $options['token'] ?? config('services.authorizedoc.api_key');

        if (! is_string($token) || $token === '') {
            throw new \InvalidArgumentException('Missing authorizedoc token. Set services.authorizedoc.api_key or pass token in $options.');
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout((int) ($options['timeout'] ?? 60))
            ->post($options['endpoint'] ?? config('services.authorizedoc.endpoint'), $payload);

        if ($response->ok()) {
            // Your current API returns base64 in $responseData['data'] (per your snippet)
            $responseData = $response->json();

            $b64 = $responseData['data']['base64'] ?? null;
            if (! is_string($b64) || $b64 === '') {
                throw new \RuntimeException('Sign service did not return base64 PDF at key: data');
            }

            $signedBinary = base64_decode($b64, true);
            if ($signedBinary === false || $signedBinary === '') {
                throw new \RuntimeException('Failed to base64-decode signed PDF.');
            }

            // Basic safety check: PDF header
            if (! str_starts_with($signedBinary, '%PDF')) {
                throw new \RuntimeException('Decoded signed data does not look like a PDF.');
            }

            // Overwrite original atomically (write temp then move)
            $tmpPath = $this->tmpPathFor($path);

            $disk->put($tmpPath, $signedBinary);

            // move() overwrites on many drivers, but to be safe we delete first
            if ($disk->exists($path)) {
                $disk->delete($path);
            }

            $disk->move($tmpPath, $path);

            return $path;
        }

        return null;

    }

    private function tmpPathFor(string $path): string
    {
        $dir = trim((string) pathinfo($path, PATHINFO_DIRNAME), '.');
        $base = pathinfo($path, PATHINFO_FILENAME);

        $tmpName = $base.'.signed-tmp-'.Str::random(8).'.pdf';

        return $dir === '' ? $tmpName : ($dir.'/'.$tmpName);
    }
}
