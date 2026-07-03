<?php

declare(strict_types=1);

namespace Tests\Unit\Rule;

use DocPlatform\Rule\Configurations\MaxSizeRule as MaxSizeConfig;
use DocPlatform\Rule\Configurations\ProhibitedWordsRule as ProhibitedWordsConfig;
use DocPlatform\Rule\Enum\RuleType;
use DocPlatform\Rule\RuleConfigurationManager;
use DocPlatform\Rule\RuleParamCaster;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RuleParamCaster::class)]
final class RuleParamCasterTest extends TestCase
{
    private RuleParamCaster $caster;

    protected function setUp(): void
    {
        $manager = new RuleConfigurationManager();
        $manager->register(RuleType::MaxSizeRule, new MaxSizeConfig());
        $manager->register(RuleType::ProhibitedWordsRule, new ProhibitedWordsConfig());

        $this->caster = new RuleParamCaster($manager);
    }

    public function test_build_form_params_converts_string_to_integer_for_integer_field(): void
    {
        $result = $this->caster->buildFormParams(['maxBytes' => '2048'], RuleType::MaxSizeRule);

        $this->assertSame(2048, $result['maxBytes']);
    }

    public function test_build_form_params_converts_newline_string_to_array_for_array_field(): void
    {
        $result = $this->caster->buildFormParams(['words' => "spam\njunk"], RuleType::ProhibitedWordsRule);

        $this->assertSame(['spam', 'junk'], $result['words']);
    }

    public function test_build_form_params_strips_empty_lines_from_array_field(): void
    {
        $result = $this->caster->buildFormParams(['words' => "spam\n\njunk\n"], RuleType::ProhibitedWordsRule);

        $this->assertSame(['spam', 'junk'], $result['words']);
    }

    public function test_build_form_params_excludes_keys_not_in_schema(): void
    {
        $result = $this->caster->buildFormParams(['maxBytes' => '512', 'unknown' => 'value'], RuleType::MaxSizeRule);

        $this->assertArrayNotHasKey('unknown', $result);
    }

    public function test_prepare_display_params_converts_array_to_newline_string(): void
    {
        $result = $this->caster->prepareDisplayParams(['words' => ['spam', 'junk']], RuleType::ProhibitedWordsRule);

        $this->assertSame("spam\njunk", $result['words']);
    }

    public function test_prepare_display_params_leaves_non_array_fields_unchanged(): void
    {
        $result = $this->caster->prepareDisplayParams(['maxBytes' => 1024], RuleType::MaxSizeRule);

        $this->assertSame(1024, $result['maxBytes']);
    }
}
