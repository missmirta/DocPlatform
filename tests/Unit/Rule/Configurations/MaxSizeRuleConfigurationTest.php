<?php

declare(strict_types=1);

namespace Tests\Unit\Rule\Configurations;

use DocPlatform\Exception\InvalidRuleParametersException;
use DocPlatform\Rule\Configurations\MaxSizeRule;
use DocPlatform\Rule\Rules\MaxSizeRule as MaxSizeRuleImpl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxSizeRule::class)]
final class MaxSizeRuleConfigurationTest extends TestCase
{
    private MaxSizeRule $config;

    protected function setUp(): void
    {
        $this->config = new MaxSizeRule();
    }

    public function test_validate_returns_no_errors_for_valid_params(): void
    {
        $this->assertSame([], $this->config->validate(['maxBytes' => 1024]));
    }

    public function test_validate_returns_error_when_maxBytes_is_missing(): void
    {
        $errors = $this->config->validate([]);

        $this->assertContains('maxBytes is required', $errors);
    }

    public function test_validate_returns_error_when_maxBytes_is_zero(): void
    {
        $errors = $this->config->validate(['maxBytes' => 0]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_maxBytes_is_negative(): void
    {
        $errors = $this->config->validate(['maxBytes' => -1]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_maxBytes_is_not_integer(): void
    {
        $errors = $this->config->validate(['maxBytes' => '1024']);

        $this->assertNotEmpty($errors);
    }

    public function test_create_returns_rule_instance(): void
    {
        $rule = $this->config->create(['maxBytes' => 512]);

        $this->assertInstanceOf(MaxSizeRuleImpl::class, $rule);
    }

    public function test_validate_and_merge_defaults_throws_on_invalid_params(): void
    {
        $this->expectException(InvalidRuleParametersException::class);

        $this->config->validateAndMergeDefaults([]);
    }

    public function test_validate_and_merge_defaults_returns_params_on_valid_input(): void
    {
        $result = $this->config->validateAndMergeDefaults(['maxBytes' => 2048]);

        $this->assertSame(['maxBytes' => 2048], $result);
    }
}
