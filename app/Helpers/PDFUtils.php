<?php

declare(strict_types=1);

if (! function_exists('pdfHasDigitalSignature')) {
    function pdfHasDigitalSignature(string $path): bool
    {
        if (! is_readable($path)) {
            throw new RuntimeException("File not readable: {$path}");
        }

        $data = file_get_contents($path);
        if ($data === false || $data === '') {
            return false;
        }

        // 1) Look for a signature dictionary: << ... /Type /Sig ... >> or << ... /FT /Sig ... >>
        $patternSigDict = '/<<[^>]*\/Type\s*\/Sig[^>]*>>/s';
        $patternSigField = '/<<[^>]*\/FT\s*\/Sig[^>]*>>/s';

        if (preg_match($patternSigDict, $data) === 1 || preg_match($patternSigField, $data) === 1) {
            return true;
        }

        // 2) Fallback: typical PKCS#7 signature structure: /ByteRange [...] + nearby /Contents <...>
        if (preg_match('/\/ByteRange\s*\[\s*(\d+\s+){3}\d+\s*\]/', $data, $m, PREG_OFFSET_CAPTURE)) {
            $byteRangePos = $m[0][1];

            // Look only in a small window after /ByteRange to avoid generic /Contents elsewhere
            $window = substr($data, $byteRangePos, 4096);

            if (preg_match('/\/Contents\s*<[^>]+>/s', $window)) {
                return true;
            }
        }

        // If we got here, we didn't see any real signature structures
        return false;
    }
    /*
    function pdfHasDigitalSignature(string $path): bool
    {
        if (! is_readable($path)) {
            throw new RuntimeException("File not readable: {$path}");
        }

        $size = filesize($path);
        if ($size === false || $size === 0) {
            return false;
        }

        $fh = fopen($path, 'rb');
        if (! $fh) {
            throw new RuntimeException("Cannot open file: {$path}");
        }

        $chunkSize = 1024 * 512; // 512 KB

        // Read first chunk
        $start = fread($fh, min($chunkSize, $size));

        // Read last chunk
        if ($size > $chunkSize) {
            fseek($fh, max(0, $size - $chunkSize));
            $end = fread($fh, $chunkSize);
        } else {
            $end = '';
        }

        fclose($fh);

        $data = $start.$end;

        // Look for typical signature markers
        $needles = [
            '/Type /Sig',   // signature object
            '/FT /Sig',     // form field of type signature
            '/Sig',         // generic signature keyword
            '/ByteRange',   // present in most signed PDFs
            '/Contents',    // signature contents
        ];

        foreach ($needles as $needle) {
            if (strpos($data, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
        */
}
