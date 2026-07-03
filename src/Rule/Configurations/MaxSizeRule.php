<?php

declare(strict_types=1);

namespace DocPlatform\Rule\Configurations;

use DocPlatform\Rule\AbstractRuleConfiguration;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;
use DocPlatform\Rule\Rules\MaxSizeRule as MaxSizeRuleImpl;

final class MaxSizeRule extends AbstractRuleConfiguration
{
    public function create(array $parameters): ValidationRuleInterface
    {
        return new MaxSizeRuleImpl($parameters['maxBytes']);
    }

    public function label(): string
    {
        return 'Maximum Content Size';
    }

    public function description(): string
    {
        return 'Rejects documents whose content exceeds the given byte limit.';
    }

    public function parameterSchema(): array
    {
        return [
            'maxBytes' => [
                'type'        => 'integer',
                'required'    => true,
                'minimum'     => 1,
                'description' => 'Maximum allowed content size in bytes',
            ],
        ];
    }

    public function validate(array $params): array
    {
        $errors = [];

        if (!array_key_exists('maxBytes', $params)) {
            $errors[] = 'maxBytes is required';
        } elseif (!is_int($params['maxBytes']) || $params['maxBytes'] < 1) {
            $errors[] = 'maxBytes must be a positive integer';
        }

        return $errors;
    }
}
