<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Contracts;

use DocPlatform\RuleConfiguration\AbstractRuleConfiguration;
use DocPlatform\RuleConfiguration\RuleType;

interface RuleConfigurationRegistryInterface
{
    public function register(RuleType $ruleType, AbstractRuleConfiguration $configuration): void;

    public function create(RuleType $ruleType, array $params): ValidationRuleInterface;

    public function configurationFor(RuleType $ruleType): AbstractRuleConfiguration;

    /** @return RuleType[] */
    public function availableTypes(): array;

    /** @return array<string, mixed> */
    public function schemaFor(RuleType $ruleType): array;

    /** @return array<string, array<string, mixed>> */
    public function allSchemas(): array;
}
