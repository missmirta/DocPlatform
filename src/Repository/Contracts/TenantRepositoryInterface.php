<?php

declare(strict_types=1);

namespace DocPlatform\Repository\Contracts;

use PDO;

interface TenantRepositoryInterface
{
    public function listTenants(): array;

    public function getConnectionFor(string $tenantId): PDO;
}
