<?php

declare(strict_types=1);

namespace DocPlatform\Rule\Configurations;

use DocPlatform\Rule\AbstractRuleConfiguration;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;
use DocPlatform\Rule\Rules\ProhibitedWordsRule as ProhibitedWordsRuleImpl;

final class ProhibitedWordsRule extends AbstractRuleConfiguration
{
    public function create(array $parameters): ValidationRuleInterface
    {
        return new ProhibitedWordsRuleImpl($parameters['words']);
    }

    public function label(): string
    {
        return 'Prohibited Words';
    }

    public function description(): string
    {
        return 'Rejects documents containing any of the listed words (case-insensitive).';
    }

    public function parameterSchema(): array
    {
        return [
            'words' => [
                'type'        => 'array',
                'items'       => 'string',
                'required'    => true,
                'minItems'    => 1,
                'description' => 'List of prohibited words',
            ],
        ];
    }

    public function validate(array $params): array
    {
        $errors = [];

        if (!array_key_exists('words', $params)) {
            $errors[] = 'words is required';
        } elseif (!is_array($params['words']) || count($params['words']) === 0) {
            $errors[] = 'words must be a non-empty array';
        } elseif (array_filter($params['words'], fn($w) => !is_string($w) || trim($w) === '')) {
            $errors[] = 'each word must be a non-empty string';
        }

        return $errors;
    }
}
