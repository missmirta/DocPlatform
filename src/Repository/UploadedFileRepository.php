<?php

declare(strict_types=1);

namespace DocPlatform\Repository;

use DocPlatform\Model\Document;
use DocPlatform\Repository\Contracts\TenantRepositoryInterface;
use DocPlatform\Repository\Contracts\UploadedFileRepositoryInterface;
use PDO;

final class UploadedFileRepository implements UploadedFileRepositoryInterface
{
    public function __construct(
        private readonly TenantRepositoryInterface $registry,
    ) {}

    public function save(string $tenantId, Document $document): void
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare(
            'INSERT INTO uploaded_files (file_id, size_bytes, metadata, uploaded_at)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $document->id,
            strlen($document->content),
            json_encode($document->metadata, JSON_THROW_ON_ERROR),
            $document->uploadedAt,
        ]);
    }

    public function findByTenant(string $tenantId): array
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->query(
            'SELECT file_id, size_bytes, metadata, uploaded_at
             FROM uploaded_files
             ORDER BY uploaded_at ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
