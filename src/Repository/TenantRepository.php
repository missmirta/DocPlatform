<?php

declare(strict_types=1);

namespace DocPlatform\Repository;

use DocPlatform\Exception\UnknownTenantException;
use DocPlatform\Repository\Contracts\TenantRepositoryInterface;
use PDO;

final class TenantRepository implements TenantRepositoryInterface
{
    private array $connections;

    public function __construct(private readonly PDO $registryPdo, array $connections = [])
    {
        $this->connections = $connections;
    }

    public function listTenants(): array
    {
        $stmt = $this->registryPdo->query('SELECT tenant_id FROM tenants ORDER BY tenant_id');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getConnectionFor(string $tenantId): PDO
    {
        if (isset($this->connections[$tenantId])) {
            return $this->connections[$tenantId];
        }

        $stmt = $this->registryPdo->prepare(
            'SELECT db_path FROM tenants WHERE tenant_id = ?'
        );
        $stmt->execute([$tenantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw new UnknownTenantException($tenantId);
        }

        $pdo = new PDO('sqlite:' . $row['db_path']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connections[$tenantId] = $pdo;

        return $pdo;
    }
}
