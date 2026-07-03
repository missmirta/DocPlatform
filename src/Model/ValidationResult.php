<?php

declare(strict_types=1);

namespace DocPlatform\Model;

final class ValidationResult
{
    public function __construct(
        public bool $isValid,
        public array $errors,
    ) {}
}
