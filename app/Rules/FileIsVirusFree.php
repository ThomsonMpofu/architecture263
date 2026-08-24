<?php

namespace App\Rules;

use App\Support\ClamAvScanner;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Rejects an uploaded file if ClamAV (docs.clamav.net) flags it as
 * infected, or if it can't be scanned at all — a file we can't verify is
 * clean is treated the same as one that isn't.
 */
class FileIsVirusFree implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $scanner = new ClamAvScanner(
            enabled: (bool) config('services.clamav.enabled'),
            host: (string) config('services.clamav.host'),
            port: (int) config('services.clamav.port'),
            timeout: (int) config('services.clamav.timeout'),
        );

        try {
            $signature = $scanner->scan($value->getRealPath());
        } catch (Throwable $e) {
            Log::error('ClamAV scan failed', ['error' => $e->getMessage()]);
            $fail('This file could not be scanned for viruses right now. Please try again shortly.');

            return;
        }

        if ($signature !== null) {
            Log::warning('ClamAV blocked an infected upload', [
                'signature' => $signature,
                'original_name' => $value->getClientOriginalName(),
            ]);

            $fail("This file was rejected because it appears to contain malware ({$signature}). Please upload a clean file.");
        }
    }
}
