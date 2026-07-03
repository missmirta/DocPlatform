<?php

declare(strict_types=1);

namespace DocPlatform\Service\Contracts;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\RuleType;

interface RuleServiceInterface
{
    public function add(string $tenantId, RuleType $ruleType, array $parameters): int;
    public function update(string $tenantId, int $ruleId, array $parameters): void;
    public function remove(string $tenantId, int $ruleId): void;
    public function index(string $tenantId): array;
    public function availableRuleTypes(): array;
    /** @return string[] */
    public function validate(string $tenantId, Document $document): array;
}
