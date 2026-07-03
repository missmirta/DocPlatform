<?php

declare(strict_types=1);

namespace DocPlatform\Rule\Rules;

use DocPlatform\Model\Document;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;

final class RequiredMetadataRule implements ValidationRuleInterface
{
    public function __construct(private readonly array $fields) {}

    public function validate(Document $document): array
    {
        $errors = [];
        foreach ($this->fields as $field) {
            if (!array_key_exists($field, $document->metadata)) {
                $errors[] = "Required metadata field '{$field}' is missing";
            }
        }
        return $errors;
    }
}
