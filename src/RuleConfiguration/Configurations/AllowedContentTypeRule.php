<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Configurations;

use DocPlatform\RuleConfiguration\AbstractRuleConfiguration;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;
use DocPlatform\RuleConfiguration\Rules\AllowedContentTypeRule as AllowedContentTypeRuleImpl;

final class AllowedContentTypeRule extends AbstractRuleConfiguration
{
    public function create(array $parameters): ValidationRuleInterface
    {
        return new AllowedContentTypeRuleImpl($parameters['types']);
    }

    public function label(): string
    {
        return 'Allowed Content Types';
    }

    public function description(): string
    {
        return 'Rejects documents whose metadata[type] is not in the allowed list.';
    }

    public function parameterSchema(): array
    {
        return [
            'types' => [
                'type'        => 'array',
                'items'       => 'string',
                'required'    => true,
                'minItems'    => 1,
                'description' => 'Allowed content-type values (e.g. pdf, docx)',
            ],
        ];
    }

    public function validate(array $params): array
    {
        $errors = [];

        if (!array_key_exists('types', $params)) {
            $errors[] = 'types is required';
        } elseif (!is_array($params['types']) || count($params['types']) === 0) {
            $errors[] = 'types must be a non-empty array';
        } elseif (array_filter($params['types'], fn($t) => !is_string($t) || trim($t) === '')) {
            $errors[] = 'each type must be a non-empty string';
        }

        return $errors;
    }
}
