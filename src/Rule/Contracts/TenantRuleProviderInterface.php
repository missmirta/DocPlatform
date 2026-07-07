<?php

declare(strict_types=1);

namespace DocPlatform\Rule\Contracts;

interface TenantRuleProviderInterface
{
    public function addRule(string $tenantId, ValidationRuleInterface $rule): void;

    /** @return ValidationRuleInterface[] */
    public function getRulesForTenant(string $tenantId): array;
}
