<?php

declare(strict_types=1);

namespace DocPlatform\Model;

final class Document
{
    public function __construct(
        public readonly string $id,
        public readonly string $tenantId, // according to Test task $tenantId should be in model.
                                          // But I have done DB per-tenant, so $tenantId is not necessary here.
        public readonly string $content,
        public readonly string $textContent,
        public readonly array $metadata,
        public readonly string $uploadedAt,
    ) {}
}
