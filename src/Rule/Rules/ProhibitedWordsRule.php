<?php

declare(strict_types=1);

namespace DocPlatform\Rule\Rules;

use DocPlatform\Model\Document;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;

final class ProhibitedWordsRule implements ValidationRuleInterface
{
    public function __construct(private readonly array $words) {}

    public function validate(Document $document): array
    {
        $errors = [];
        $lower = strtolower($document->textContent);
        foreach ($this->words as $word) {
            if (str_contains($lower, strtolower($word))) {
                $errors[] = "Prohibited word '{$word}' found in document content";
            }
        }
        return $errors;
    }
}
