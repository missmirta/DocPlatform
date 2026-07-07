<?php

declare(strict_types=1);

namespace DocPlatform\Service\Contracts;

use DocPlatform\Model\Document;
use DocPlatform\Model\ValidationResult;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;

interface ValidatorServiceInterface
{
    public function validate(Document $document, ValidationRuleInterface ...$rules): ValidationResult;
}
