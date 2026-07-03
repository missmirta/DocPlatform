<?php

declare(strict_types=1);

namespace DocPlatform\Rule;

use Closure;
use DocPlatform\Rule\Contracts\RuleConfigurationRegistryInterface;
use DocPlatform\Rule\Enum\RuleType;

final class RuleParamCaster
{
    public function __construct(
        private readonly RuleConfigurationRegistryInterface $registry,
    ) {}

    public function buildFormParams(array $rawParams, RuleType $ruleType): array
    {
        $schema = $this->registry->configurationFor($ruleType)->parameterSchema();
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
        $schema = $this->registry->configurationFor($ruleType)->parameterSchema();

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
