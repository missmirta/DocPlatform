<?php

declare(strict_types=1);

namespace Tests\Unit\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Rules\AllowedContentTypeRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AllowedContentTypeRule::class)]
final class AllowedContentTypeRuleTest extends TestCase
{
    private function doc(array $metadata): Document
    {
        return new Document('id', 'tenant', '', '', $metadata, '2024-01-01');
    }

    public function test_validate_passes_when_type_is_in_allowed_list(): void
    {
        $rule = new AllowedContentTypeRule(['pdf', 'docx']);

        $this->assertSame([], $rule->validate($this->doc(['type' => 'pdf'])));
    }

    public function test_validate_fails_when_type_is_not_in_allowed_list(): void
    {
        $rule = new AllowedContentTypeRule(['pdf', 'docx']);

        $errors = $rule->validate($this->doc(['type' => 'xlsx']));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('xlsx', $errors[0]);
    }

    public function test_validate_fails_when_type_metadata_key_is_missing(): void
    {
        $rule = new AllowedContentTypeRule(['pdf']);

        $errors = $rule->validate($this->doc([]));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('missing', $errors[0]);
    }

    public function test_validate_error_lists_allowed_types(): void
    {
        $rule = new AllowedContentTypeRule(['pdf', 'docx']);

        $errors = $rule->validate($this->doc(['type' => 'xlsx']));

        $this->assertStringContainsString('pdf', $errors[0]);
        $this->assertStringContainsString('docx', $errors[0]);
    }

    public function test_validate_type_comparison_is_strict(): void
    {
        $rule = new AllowedContentTypeRule(['1']);

        $errors = $rule->validate($this->doc(['type' => 1]));

        $this->assertCount(1, $errors);
    }
}
