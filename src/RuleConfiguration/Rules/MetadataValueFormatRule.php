<?php

declare(strict_types=1);

namespace DocPlatform\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;

final class MetadataValueFormatRule implements ValidationRuleInterface
{
    public function __construct(
        private readonly string $field,
        private readonly string $pattern,
    ) {
        if (@preg_match($pattern, '') === false) {
            throw new \InvalidArgumentException(
                "Invalid regex pattern for field '{$field}': {$pattern}"
            );
        }
    }

    public function validate(Document $document): array
    {
        if (!array_key_exists($this->field, $document->metadata)) {
            return ["Metadata field '{$this->field}' is missing (required format: {$this->pattern})"];
        }
        $value = $document->metadata[$this->field];
        if (preg_match($this->pattern, (string) $value) === 1) {
            return [];
        }
        return ["Metadata field '{$this->field}' does not match required format {$this->pattern}"];
    }
}
