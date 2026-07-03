<?php

declare(strict_types=1);

namespace DocPlatform\Model;

use DocPlatform\RuleConfiguration\RuleType;

final class ValidationRule
{
    public function __construct(
        public readonly int $id,
        public readonly RuleType $ruleType,
        public readonly array $parameters,
        public readonly int $sortOrder,
    ) {}
}
