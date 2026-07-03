<?php

declare(strict_types=1);

namespace DocPlatform\Service\Contracts;

use DocPlatform\Model\Document;
use DocPlatform\Model\ValidationResult;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;

interface ValidatorServiceInterface
{
    /** @param ValidationRuleInterface[] $rules */
    public function validate(Document $document, array $rules): ValidationResult;
}
