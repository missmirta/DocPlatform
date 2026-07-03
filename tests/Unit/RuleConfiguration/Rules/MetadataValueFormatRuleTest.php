<?php

declare(strict_types=1);

namespace Tests\Unit\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Rules\MetadataValueFormatRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetadataValueFormatRule::class)]
final class MetadataValueFormatRuleTest extends TestCase
{
    private function doc(array $metadata): Document
    {
        return new Document('id', 'tenant', '', '', $metadata, '2024-01-01');
    }

    public function test_validate_passes_when_field_matches_pattern(): void
    {
        $rule = new MetadataValueFormatRule('ref', '/^REF-\d{4}$/');

        $this->assertSame([], $rule->validate($this->doc(['ref' => 'REF-1234'])));
    }

    public function test_validate_fails_when_field_does_not_match_pattern(): void
    {
        $rule = new MetadataValueFormatRule('ref', '/^REF-\d{4}$/');

        $errors = $rule->validate($this->doc(['ref' => 'REF-12']));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('ref', $errors[0]);
    }

    public function test_validate_fails_when_required_field_is_missing(): void
    {
        $rule = new MetadataValueFormatRule('ref', '/^REF-\d{4}$/');

        $errors = $rule->validate($this->doc([]));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('missing', $errors[0]);
    }

    public function test_validate_casts_field_value_to_string(): void
    {
        $rule = new MetadataValueFormatRule('count', '/^\d+$/');

        $this->assertSame([], $rule->validate($this->doc(['count' => 42])));
    }

    public function test_constructor_throws_on_invalid_regex(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MetadataValueFormatRule('ref', '[invalid');
    }
}
