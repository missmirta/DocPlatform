<?php

declare(strict_types=1);

namespace DocPlatform\Http\Requests;

use DocPlatform\Exception\InvalidRuleParametersException;
use DocPlatform\Exception\UnknownRuleTypeException;
use DocPlatform\Rule\Enum\RuleType;

final class StoreRuleRequest
{
    private function __construct(
        public readonly RuleType $ruleType,
        public readonly array $parameters,
    ) {
    }

    public static function fromBody(array $body): self
    {
        if (empty($body['rule_type'])) {
            throw new InvalidRuleParametersException(['rule_type is required']);
        }

        $ruleType = RuleType::tryFrom($body['rule_type']);
        if ($ruleType === null) {
            throw new UnknownRuleTypeException($body['rule_type']);
        }

        return new self(
            ruleType:   $ruleType,
            parameters: $body['parameters'] ?? [],
        );
    }
}
