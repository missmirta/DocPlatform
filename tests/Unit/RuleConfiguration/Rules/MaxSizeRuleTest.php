<?php

declare(strict_types=1);

namespace Tests\Unit\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Rules\MaxSizeRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxSizeRule::class)]
final class MaxSizeRuleTest extends TestCase
{
    private function doc(string $content): Document
    {
        return new Document('id', $content, '', [], '2024-01-01');
    }

    public function test_validate_passes_when_content_is_within_limit(): void
    {
        $rule = new MaxSizeRule(10);

        $this->assertSame([], $rule->validate($this->doc('hello')));
    }

    public function test_validate_passes_when_content_length_equals_limit(): void
    {
        $rule = new MaxSizeRule(5);

        $this->assertSame([], $rule->validate($this->doc('hello')));
    }

    public function test_validate_fails_when_content_exceeds_limit(): void
    {
        $rule = new MaxSizeRule(4);

        $errors = $rule->validate($this->doc('hello'));

        $this->assertCount(1, $errors);
    }

    public function test_validate_error_contains_limit_bytes(): void
    {
        $rule = new MaxSizeRule(4);

        $errors = $rule->validate($this->doc('hello'));

        $this->assertStringContainsString('4 bytes', $errors[0]);
    }

    public function test_validate_error_contains_actual_size(): void
    {
        $rule = new MaxSizeRule(2);

        $errors = $rule->validate($this->doc('hello'));

        $this->assertStringContainsString('actual: 5', $errors[0]);
    }

    public function test_validate_passes_for_empty_content(): void
    {
        $rule = new MaxSizeRule(1);

        $this->assertSame([], $rule->validate($this->doc('')));
    }
}
