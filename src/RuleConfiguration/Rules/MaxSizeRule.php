<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;

final class MaxSizeRule implements ValidationRuleInterface
{
    public function __construct(private readonly int $maxBytes) {}

    public function validate(Document $document): array
    {
        $actual = strlen($document->content);
        if ($actual <= $this->maxBytes) {
            return [];
        }
        return ["Document exceeds maximum size of {$this->maxBytes} bytes (actual: {$actual})"];
    }
}
