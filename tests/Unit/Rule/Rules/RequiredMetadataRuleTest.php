<?php

declare(strict_types=1);

namespace Tests\Unit\Rule\Rules;

use DocPlatform\Model\Document;
use DocPlatform\Rule\Rules\RequiredMetadataRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequiredMetadataRule::class)]
final class RequiredMetadataRuleTest extends TestCase
{
    private function doc(array $metadata): Document
    {
        return new Document('id', 'tenant', '', '', $metadata, '2024-01-01');
    }

    public function test_validate_passes_when_all_required_fields_are_present(): void
    {
        $rule = new RequiredMetadataRule(['author', 'title']);

        $this->assertSame([], $rule->validate($this->doc(['author' => 'Alice', 'title' => 'Doc'])));
    }

    public function test_validate_fails_when_a_required_field_is_missing(): void
    {
        $rule = new RequiredMetadataRule(['author']);

        $errors = $rule->validate($this->doc([]));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('author', $errors[0]);
    }

    public function test_validate_reports_each_missing_field(): void
    {
        $rule = new RequiredMetadataRule(['author', 'title']);

        $errors = $rule->validate($this->doc([]));

        $this->assertCount(2, $errors);
    }

    public function test_validate_passes_when_extra_metadata_fields_exist(): void
    {
        $rule = new RequiredMetadataRule(['author']);

        $this->assertSame([], $rule->validate($this->doc(['author' => 'Bob', 'extra' => 'value'])));
    }

    public function test_validate_treats_null_value_as_present_field(): void
    {
        $rule = new RequiredMetadataRule(['author']);

        $this->assertSame([], $rule->validate($this->doc(['author' => null])));
    }
}
