<?php

declare(strict_types=1);

namespace Tests\Unit\Rule\Configurations;

use DocPlatform\Exceptions\InvalidRuleParametersException;
use DocPlatform\Rule\Configurations\RequiredMetadataRule;
use DocPlatform\Rule\Rules\RequiredMetadataRule as RequiredMetadataRuleImpl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequiredMetadataRule::class)]
final class RequiredMetadataRuleConfigurationTest extends TestCase
{
    private RequiredMetadataRule $config;

    protected function setUp(): void
    {
        $this->config = new RequiredMetadataRule();
    }

    public function test_validate_returns_no_errors_for_valid_params(): void
    {
        $this->assertSame([], $this->config->validate(['fields' => ['author', 'title']]));
    }

    public function test_validate_returns_error_when_fields_is_missing(): void
    {
        $errors = $this->config->validate([]);

        $this->assertContains('fields is required', $errors);
    }

    public function test_validate_returns_error_when_fields_is_empty_array(): void
    {
        $errors = $this->config->validate(['fields' => []]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_fields_contains_empty_string(): void
    {
        $errors = $this->config->validate(['fields' => ['author', '']]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_fields_is_not_array(): void
    {
        $errors = $this->config->validate(['fields' => 'author']);

        $this->assertNotEmpty($errors);
    }

    public function test_create_returns_rule_instance(): void
    {
        $rule = $this->config->create(['fields' => ['author']]);

        $this->assertInstanceOf(RequiredMetadataRuleImpl::class, $rule);
    }

    public function test_validate_and_merge_defaults_throws_on_invalid_params(): void
    {
        $this->expectException(InvalidRuleParametersException::class);

        $this->config->validateAndMergeDefaults([]);
    }
}
