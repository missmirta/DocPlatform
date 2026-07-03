<?php

declare(strict_types=1);

namespace DocPlatform\Http\Requests;

use finfo;
use ZipArchive;

final class UploadRequest
{
    private function __construct(
        public readonly string $tenantId,
        public readonly string $content,
        public readonly string $textContent,
        public readonly array $metadata,
    ) {}

    public static function fromRaw(array $post, array $files): self
    {
        $tmpPath = $files['document']['tmp_name'];

        $metadata = self::parseMetadata($post);
        $metadata['type'] = self::detectFileType($tmpPath);
        $metadata = array_filter(
            $metadata,
            static fn(string $v, string $k): bool => $k !== '' && trim($v) !== '',
            ARRAY_FILTER_USE_BOTH,
        );

        $content = file_get_contents($tmpPath);

        return new self(
            tenantId:    $post['tenant'] ?? '',
            content:     $content,
            textContent: self::extractTextContent($tmpPath),
            metadata:    $metadata,
        );
    }

    private static function extractTextContent(string $tmpPath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            return file_get_contents($tmpPath);
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return file_get_contents($tmpPath);
        }

        return strip_tags($xml);
    }

    private static function parseMetadata(array $post): array
    {
        $metadata = $post['metadata'] ?? [];

        foreach (explode("\n", $post['metadata_extra'] ?? '') as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) === 2 && trim($parts[0]) !== '') {
                $metadata[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $metadata;
    }

    private static function detectFileType(string $tmpPath): string
    {
        $ext = (new finfo(FILEINFO_EXTENSION))->file($tmpPath);

        return explode('/', $ext)[0];
    }
}
