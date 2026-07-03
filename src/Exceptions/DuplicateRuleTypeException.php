<?php

declare(strict_types=1);

namespace DocPlatform\Exceptions;

final class DuplicateRuleTypeException extends \RuntimeException
{
    public function __construct(string $ruleType)
    {
        parent::__construct("A rule of type '{$ruleType}' already exists for this tenant.");
    }
}
