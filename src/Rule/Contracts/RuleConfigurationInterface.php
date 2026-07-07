<?php

declare(strict_types=1);

namespace DocPlatform\Rule\Contracts;

interface RuleConfigurationInterface
{
    public function label(): string;

    public function description(): string;

    /** @param array<string, mixed> $parameters */
    public function create(array $parameters): ValidationRuleInterface;

    /** @return array<string, mixed> */
    public function parameterSchema(): array;

    /**
     * @param  array<string, mixed> $params
     * @return string[]
     */
    public function validate(array $params): array;
}
