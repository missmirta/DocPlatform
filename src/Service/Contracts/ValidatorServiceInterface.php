<?php

declare(strict_types=1);

namespace DocPlatform\Service\Contracts;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;

interface ValidatorServiceInterface
{
    /** @param ValidationRuleInterface[] $rules */
    public function errors(Document $document, array $rules): array;

    /** @param ValidationRuleInterface[] $rules */
    public function isValid(Document $document, array $rules): bool;
}
