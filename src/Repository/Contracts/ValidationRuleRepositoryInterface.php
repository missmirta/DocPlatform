<?php

declare(strict_types=1);

namespace DocPlatform\Repository\Contracts;

use DocPlatform\Model\ValidationRule;
use DocPlatform\RuleConfiguration\RuleType;

interface ValidationRuleRepositoryInterface
{
    public function addRule(string $tenantId, RuleType $ruleType, array $parameters): int;
    public function updateRule(string $tenantId, int $ruleId, array $parameters): void;
    public function removeRule(string $tenantId, int $ruleId): void;
    /** @return ValidationRule[] */
    public function listRules(string $tenantId): array;
    public function findById(string $tenantId, int $ruleId): ?ValidationRule;
    public function getRulesFor(string $tenantId): array;
}
