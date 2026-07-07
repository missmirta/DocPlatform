<?php

declare(strict_types=1);

namespace DocPlatform\Service;

use DocPlatform\Model\Document;
use DocPlatform\Model\ValidationResult;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;
use DocPlatform\Service\Contracts\ValidatorServiceInterface;

final class ValidatorService implements ValidatorServiceInterface
{
    public function validate(Document $document, ValidationRuleInterface ...$rules): ValidationResult
    {
        $errors = [];
        foreach ($rules as $rule) {
            $errors = array_merge($errors, $rule->validate($document));
        }
        return new ValidationResult(empty($errors), $errors);
    }
}
