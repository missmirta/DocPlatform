<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Configurations;

use DocPlatform\RuleConfiguration\AbstractRuleConfiguration;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;
use DocPlatform\RuleConfiguration\Rules\RequiredMetadataRule as RequiredMetadataRuleImpl;

final class RequiredMetadataRule extends AbstractRuleConfiguration
{
    public function create(array $parameters): ValidationRuleInterface
    {
        return new RequiredMetadataRuleImpl($parameters['fields']);
    }

    public function label(): string
    {
        return 'Required Metadata Fields';
    }

    public function description(): string
    {
        return 'Rejects documents missing any of the listed metadata keys.';
    }

    public function parameterSchema(): array
    {
        return [
            'fields' => [
                'type'        => 'array',
                'items'       => 'string',
                'required'    => true,
                'minItems'    => 1,
                'description' => 'Metadata field names that must be present',
            ],
        ];
    }

    public function validate(array $params): array
    {
        $errors = [];

        if (!array_key_exists('fields', $params)) {
            $errors[] = 'fields is required';
        } elseif (!is_array($params['fields']) || count($params['fields']) === 0) {
            $errors[] = 'fields must be a non-empty array';
        } elseif (array_filter($params['fields'], fn($f) => !is_string($f) || trim($f) === '')) {
            $errors[] = 'each field name must be a non-empty string';
        }

        return $errors;
    }
}
