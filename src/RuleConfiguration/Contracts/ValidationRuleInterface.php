<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Contracts;

use DocPlatform\Model\Document;

interface ValidationRuleInterface
{
    /** @return string[] empty array means the rule passed */
    public function validate(Document $document): array;
}
