<?php

declare(strict_types=1);

namespace Tests\Unit\Rule\Configurations;

use DocPlatform\Exception\InvalidRuleParametersException;
use DocPlatform\Rule\Configurations\AllowedContentTypeRule;
use DocPlatform\Rule\Rules\AllowedContentTypeRule as AllowedContentTypeRuleImpl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AllowedContentTypeRule::class)]
final class AllowedContentTypeRuleConfigurationTest extends TestCase
{
    private AllowedContentTypeRule $config;

    protected function setUp(): void
    {
        $this->config = new AllowedContentTypeRule();
    }

    public function test_validate_returns_no_errors_for_valid_params(): void
    {
        $this->assertSame([], $this->config->validate(['types' => ['pdf', 'docx']]));
    }

    public function test_validate_returns_error_when_types_is_missing(): void
    {
        $errors = $this->config->validate([]);

        $this->assertContains('types is required', $errors);
    }

    public function test_validate_returns_error_when_types_is_empty_array(): void
    {
        $errors = $this->config->validate(['types' => []]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_types_contains_empty_string(): void
    {
        $errors = $this->config->validate(['types' => ['pdf', '']]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_types_is_not_array(): void
    {
        $errors = $this->config->validate(['types' => 'pdf']);

        $this->assertNotEmpty($errors);
    }

    public function test_create_returns_rule_instance(): void
    {
        $rule = $this->config->create(['types' => ['pdf']]);

        $this->assertInstanceOf(AllowedContentTypeRuleImpl::class, $rule);
    }

    public function test_validate_and_merge_defaults_throws_on_invalid_params(): void
    {
        $this->expectException(InvalidRuleParametersException::class);

        $this->config->validateAndMergeDefaults([]);
    }
}
