<?php

declare(strict_types=1);

namespace DocPlatform\Exception;

final class RuleNotFoundException extends \RuntimeException
{
    public function __construct(int $ruleId)
    {
        parent::__construct("Rule with ID {$ruleId} was not found for this tenant.");
    }
}
