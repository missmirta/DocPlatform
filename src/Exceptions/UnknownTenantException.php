<?php

declare(strict_types=1);

namespace DocPlatform\Exceptions;

use RuntimeException;

final class UnknownTenantException extends RuntimeException
{
    public function __construct(string $tenantId)
    {
        parent::__construct("No rules registered for tenant '{$tenantId}'");
    }
}
