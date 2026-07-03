<?php

declare(strict_types=1);

namespace DocPlatform\Service;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;
use DocPlatform\Service\Contracts\ValidatorServiceInterface;

final class ValidatorService implements ValidatorServiceInterface
{
    /** @param ValidationRuleInterface[] $rules */
    public function errors(Document $document, array $rules): array
    {
        $errors = [];
        foreach ($rules as $rule) {
            $errors = array_merge($errors, $rule->validate($document));
        }
        return $errors;
    }

    /** @param ValidationRuleInterface[] $rules */
    public function isValid(Document $document, array $rules): bool
    {
        return empty($this->errors($document, $rules));
    }
}
