<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Configurations;

use DocPlatform\RuleConfiguration\AbstractRuleConfiguration;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;
use DocPlatform\RuleConfiguration\Rules\MetadataValueFormatRule as MetadataValueFormatRuleImpl;

final class MetadataValueFormatRule extends AbstractRuleConfiguration
{
    public function create(array $parameters): ValidationRuleInterface
    {
        return new MetadataValueFormatRuleImpl($parameters['field'], $parameters['pattern']);
    }

    public function label(): string
    {
        return 'Metadata Value Format';
    }

    public function description(): string
    {
        return 'Rejects documents whose metadata field value does not match a regex pattern.';
    }

    public function parameterSchema(): array
    {
        return [
            'field'   => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'Metadata key to check',
            ],
            'pattern' => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'PCRE regex pattern (e.g. /^REF-\\d{4}$/)',
            ],
        ];
    }

    public function validate(array $params): array
    {
        $errors = [];

        if (!array_key_exists('field', $params)) {
            $errors[] = 'field is required';
        } elseif (!is_string($params['field']) || trim($params['field']) === '') {
            $errors[] = 'field must be a non-empty string';
        }

        if (!array_key_exists('pattern', $params)) {
            $errors[] = 'pattern is required';
        } elseif (!is_string($params['pattern']) || trim($params['pattern']) === '') {
            $errors[] = 'pattern must be a non-empty string';
        } else {
            set_error_handler(static fn() => true);
            try {
                $valid = preg_match($params['pattern'], '') !== false;
            } finally {
                restore_error_handler();
            }
            if (!$valid) {
                $errors[] = 'pattern is not a valid PCRE regex';
            }
        }

        return $errors;
    }
}
