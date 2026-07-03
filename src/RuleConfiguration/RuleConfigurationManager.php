<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration;

use DocPlatform\Exceptions\UnknownRuleTypeException;
use DocPlatform\RuleConfiguration\Contracts\RuleConfigurationRegistryInterface;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;

final class RuleConfigurationManager implements RuleConfigurationRegistryInterface
{
    /** @var array<string, AbstractRuleConfiguration> */
    private array $configurations = [];

    public function register(RuleType $ruleType, AbstractRuleConfiguration $configuration): void
    {
        $this->configurations[$ruleType->value] = $configuration;
    }

    /** @throws UnknownRuleTypeException */
    public function create(RuleType $ruleType, array $params): ValidationRuleInterface
    {
        return $this->configurationFor($ruleType)->create($params);
    }

    /** @throws UnknownRuleTypeException */
    public function configurationFor(RuleType $ruleType): AbstractRuleConfiguration
    {
        return $this->configurations[$ruleType->value] ?? throw new UnknownRuleTypeException($ruleType->value);
    }

    /** @return RuleType[] */
    public function availableTypes(): array
    {
        return array_map(
            fn(string $value) => RuleType::from($value),
            array_keys($this->configurations),
        );
    }

    /** @return array<string, mixed> */
    public function schemaFor(RuleType $ruleType): array
    {
        $config = $this->configurationFor($ruleType);

        return [
            SchemaField::Type->value        => $ruleType->value,
            SchemaField::Label->value       => $config->label(),
            SchemaField::Description->value => $config->description(),
            SchemaField::Parameters->value  => $config->parameterSchema(),
            SchemaField::Defaults->value    => $config->defaults(),
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public function allSchemas(): array
    {
        $schemas = [];
        foreach ($this->availableTypes() as $type) {
            $schemas[$type->value] = $this->schemaFor($type);
        }
        return $schemas;
    }
}
