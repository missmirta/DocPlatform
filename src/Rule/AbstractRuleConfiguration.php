<?php

declare(strict_types=1);

namespace DocPlatform\Rule;

use DocPlatform\Exceptions\InvalidRuleParametersException;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;

abstract class AbstractRuleConfiguration
{
    abstract public function label(): string;
    abstract public function description(): string;

    abstract public function create(array $parameters): ValidationRuleInterface;

    /**
     * Returns a JSON Schema-style descriptor of accepted parameters.
     * Used by external consumers (e.g. API responses, form builders) to
     * discover what parameters this rule accepts. Not used in validation itself.
     *
     * Shape: ['paramName' => ['type' => 'integer|string|array', 'required' => bool, ...]]
     */
    abstract public function parameterSchema(): array;

    /**
     * Validates merged parameters. Returns error messages; an empty array means valid.
     *
     * @param  array<string, mixed> $params
     * @return string[]
     */
    abstract public function validate(array $params): array;

    /** @return array<string, mixed> */
    public function defaults(): array
    {
        return [];
    }

    /**
     * Merges defaults, validates, and returns final params.
     *
     * @param  array<string, mixed> $params
     * @return array<string, mixed>
     * @throws InvalidRuleParametersException
     */
    final public function validateAndMergeDefaults(array $params): array
    {
        $merged = array_merge($this->defaults(), $params);
        $errors = $this->validate($merged);
        if (!empty($errors)) {
            throw new InvalidRuleParametersException($errors);
        }
        return $merged;
    }
}
