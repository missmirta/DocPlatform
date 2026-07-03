<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration;

use Closure;
use DocPlatform\Exceptions\UnknownRuleTypeException;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;

final class RuleConfigurationManager
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
        return $this->configurations[$ruleType->value]
            ?? throw new UnknownRuleTypeException($ruleType->value);
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
            'type'        => $ruleType->value,
            'label'       => $config->label(),
            'description' => $config->description(),
            'parameters'  => $config->parameterSchema(),
            'defaults'    => $config->defaults(),
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

    public function buildFormParams(array $rawParams, RuleType $ruleType): array
    {
        $schema = $this->configurationFor($ruleType)->parameterSchema();
        $params = array_intersect_key($rawParams, $schema);

        return $this->castParams($params, $schema, function (mixed $value, string $type): mixed {
            if ($type === 'integer' && is_string($value)) {
                return (int) $value;
            }
            if ($type === 'array' && is_string($value)) {
                return array_values(array_filter(array_map('trim', explode("\n", $value))));
            }
            return $value;
        });
    }

    public function prepareDisplayParams(array $params, RuleType $ruleType): array
    {
        $schema = $this->configurationFor($ruleType)->parameterSchema();

        return $this->castParams($params, $schema, function (mixed $value, string $type): mixed {
            if ($type === 'array' && is_array($value)) {
                return implode("\n", $value);
            }
            return $value;
        });
    }

    private function castParams(array $params, array $schema, Closure $cast): array
    {
        foreach ($params as $field => $value) {
            $params[$field] = $cast($value, $schema[$field]['type'] ?? '');
        }
        return $params;
    }
}
