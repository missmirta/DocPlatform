<?php

declare(strict_types=1);

namespace Tests\Unit\Rule\Configurations;

use DocPlatform\Rule\Configurations\ProhibitedWordsRule;
use DocPlatform\Rule\Rules\ProhibitedWordsRule as ProhibitedWordsRuleImpl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProhibitedWordsRule::class)]
final class ProhibitedWordsRuleConfigurationTest extends TestCase
{
    private ProhibitedWordsRule $config;

    protected function setUp(): void
    {
        $this->config = new ProhibitedWordsRule();
    }

    public function test_validate_returns_no_errors_for_valid_params(): void
    {
        $this->assertSame([], $this->config->validate(['words' => ['spam', 'junk']]));
    }

    public function test_validate_returns_error_when_words_is_missing(): void
    {
        $errors = $this->config->validate([]);

        $this->assertContains('words is required', $errors);
    }

    public function test_validate_returns_error_when_words_is_empty_array(): void
    {
        $errors = $this->config->validate(['words' => []]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_words_contains_empty_string(): void
    {
        $errors = $this->config->validate(['words' => ['spam', '']]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_returns_error_when_words_is_not_array(): void
    {
        $errors = $this->config->validate(['words' => 'spam']);

        $this->assertNotEmpty($errors);
    }

    public function test_create_returns_rule_instance(): void
    {
        $rule = $this->config->create(['words' => ['spam']]);

        $this->assertInstanceOf(ProhibitedWordsRuleImpl::class, $rule);
    }
}
