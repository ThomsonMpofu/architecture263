<?php

namespace App\Support;

use RuntimeException;

/**
 * Talks directly to a clamd daemon over TCP using the INSTREAM protocol
 * (see https://docs.clamav.net/manual/Usage/Scanning.html and the clamd
 * protocol docs) — no clamd/clamscan binary or Composer package needed on
 * the app side, just network access to the daemon.
 */
class ClamAvScanner
{
    private const CHUNK_SIZE = 8192;

    public function __construct(
        private readonly bool $enabled,
        private readonly string $host,
        private readonly int $port,
        private readonly int $timeout,
    ) {
    }

    /**
     * Scan a local file. Returns the detected signature name if infected,
     * or null if the file is clean (or scanning is disabled).
     *
     * @throws RuntimeException if clamd can't be reached or errors out —
     *         callers should treat this as "could not verify the file is
     *         safe", not as "the file is clean".
     */
    public function scan(string $path): ?string
    {
        if (! $this->enabled) {
            return null;
        }

        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

        if (! $socket) {
            throw new RuntimeException("Unable to reach ClamAV at {$this->host}:{$this->port} ({$errstr}).");
        }

        stream_set_timeout($socket, $this->timeout);

        try {
            fwrite($socket, "zINSTREAM\0");

            $handle = fopen($path, 'rb');

            if (! $handle) {
                throw new RuntimeException("Unable to read file for scanning: {$path}");
            }

            try {
                while (! feof($handle)) {
                    $chunk = fread($handle, self::CHUNK_SIZE);

                    if ($chunk === false || $chunk === '') {
                        break;
                    }

                    fwrite($socket, pack('N', strlen($chunk)).$chunk);
                }
            } finally {
                fclose($handle);
            }

            // A zero-length chunk tells clamd the stream is finished.
            fwrite($socket, pack('N', 0));

            $response = '';

            while (! feof($socket)) {
                $response .= fread($socket, 4096);
            }
        } finally {
            fclose($socket);
        }

        $response = trim($response, "\0 \r\n");

        if ($response === '' ) {
            throw new RuntimeException('ClamAV returned an empty response.');
        }

        if (str_contains($response, 'FOUND')) {
            preg_match('/stream:\s*(.+?)\s+FOUND/', $response, $matches);

            return $matches[1] ?? 'malware';
        }

        if (str_contains($response, 'ERROR')) {
            throw new RuntimeException("ClamAV scan error: {$response}");
        }

        return null; // "stream: OK"
    }
}
