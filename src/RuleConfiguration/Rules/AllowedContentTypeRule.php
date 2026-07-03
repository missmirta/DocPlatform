<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;

final class AllowedContentTypeRule implements ValidationRuleInterface
{
    public function __construct(private readonly array $types) {}

    public function validate(Document $document): array
    {
        if (!array_key_exists('type', $document->metadata)) {
            return ["Metadata field 'type' is missing; allowed types: " . implode(', ', $this->types)];
        }
        $actual = $document->metadata['type'];
        if (in_array($actual, $this->types, strict: true)) {
            return [];
        }
        return ["Metadata field 'type' value '{$actual}' is not in allowed types: " . implode(', ', $this->types)];
    }
}
