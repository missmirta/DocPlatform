<?php

declare(strict_types=1);

namespace DocPlatform\Exceptions;

use RuntimeException;

final class UnknownRuleTypeException extends RuntimeException
{
    public function __construct(string $ruleType)
    {
        parent::__construct("Unknown rule type '{$ruleType}'");
    }
}
