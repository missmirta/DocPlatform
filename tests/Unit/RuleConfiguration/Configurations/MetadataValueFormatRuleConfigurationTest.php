<?php

declare(strict_types=1);

namespace Tests\Unit\RuleConfiguration\Configurations;

use DocPlatform\Exceptions\InvalidRuleParametersException;
use DocPlatform\RuleConfiguration\Configurations\MetadataValueFormatRule;
use DocPlatform\RuleConfiguration\Rules\MetadataValueFormatRule as MetadataValueFormatRuleImpl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetadataValueFormatRule::class)]
final class MetadataValueFormatRuleConfigurationTest extends TestCase
{
    private MetadataValueFormatRule $config;

    protected function setUp(): void
    {
        $this->config = new MetadataValueFormatRule();
    }

    public function test_validate_returns_no_errors_for_valid_params(): void
    {
        $this->assertSame([], $this->config->validate(['field' => 'ref', 'pattern' => '/^REF-\d+$/']));
    }

    public function test_validate_returns_error_when_field_is_missing(): void
    {
        $errors = $this->config->validate(['pattern' => '/^\d+$/']);

        $this->assertContains('field is required', $errors);
    }

    public function test_validate_returns_error_when_field_is_empty_string(): void
    {
        $errors = $this->config->validate(['field' => '', 'pattern' => '/^\d+$/']);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_pattern_is_missing(): void
    {
        $errors = $this->config->validate(['field' => 'ref']);

        $this->assertContains('pattern is required', $errors);
    }

    public function test_validate_returns_error_when_pattern_is_empty_string(): void
    {
        $errors = $this->config->validate(['field' => 'ref', 'pattern' => '']);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_pattern_is_invalid_regex(): void
    {
        $errors = $this->config->validate(['field' => 'ref', 'pattern' => '[invalid']);

        $this->assertContains('pattern is not a valid PCRE regex', $errors);
    }

    public function test_create_returns_rule_instance(): void
    {
        $rule = $this->config->create(['field' => 'ref', 'pattern' => '/^\d+$/']);

        $this->assertInstanceOf(MetadataValueFormatRuleImpl::class, $rule);
    }

    public function test_validate_and_merge_defaults_throws_on_invalid_params(): void
    {
        $this->expectException(InvalidRuleParametersException::class);

        $this->config->validateAndMergeDefaults([]);
    }
}
