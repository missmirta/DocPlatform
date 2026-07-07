<?php

declare(strict_types=1);

namespace DocPlatform\Rule;

use DocPlatform\Rule\Contracts\TenantRuleProviderInterface;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;

final class TenantRuleProvider implements TenantRuleProviderInterface
{
    /** @var array<string, ValidationRuleInterface[]> */
    private array $rules = [];

    public function addRule(string $tenantId, ValidationRuleInterface $rule): void
    {
        $this->rules[$tenantId][] = $rule;
    }

    /** @return ValidationRuleInterface[] */
    public function getRulesForTenant(string $tenantId): array
    {
        return $this->rules[$tenantId] ?? [];
    }
}
