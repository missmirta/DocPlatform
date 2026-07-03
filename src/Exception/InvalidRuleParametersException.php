<?php

declare(strict_types=1);

namespace DocPlatform\Exception;

final class InvalidRuleParametersException extends \InvalidArgumentException
{
    /** @param string[] $errors */
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Invalid rule parameters: ' . implode('; ', $errors));
    }

    /** @return string[] */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
