<?php

declare(strict_types=1);

namespace DocPlatform\Repository\Contracts;

use DocPlatform\Model\Document;

interface UploadedFileRepositoryInterface
{
    public function save(string $tenantId, Document $document): void;

    public function findByTenant(string $tenantId): array;
}