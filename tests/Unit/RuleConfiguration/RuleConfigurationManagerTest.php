<?php

declare(strict_types=1);

namespace Tests\Unit\RuleConfiguration;

use DocPlatform\Exceptions\UnknownRuleTypeException;
use DocPlatform\RuleConfiguration\Configurations\MaxSizeRule as MaxSizeConfig;
use DocPlatform\RuleConfiguration\Configurations\ProhibitedWordsRule as ProhibitedWordsConfig;
use DocPlatform\RuleConfiguration\RuleConfigurationManager;
use DocPlatform\RuleConfiguration\RuleType;
use DocPlatform\RuleConfiguration\Rules\MaxSizeRule as MaxSizeRuleImpl;
use DocPlatform\RuleConfiguration\SchemaField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RuleConfigurationManager::class)]
final class RuleConfigurationManagerTest extends TestCase
{
    private RuleConfigurationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new RuleConfigurationManager();
    }

    public function test_configuration_for_returns_registered_configuration(): void
    {
        $config = new MaxSizeConfig();
        $this->manager->register(RuleType::MaxSizeRule, $config);

        $this->assertSame($config, $this->manager->configurationFor(RuleType::MaxSizeRule));
    }

    public function test_configuration_for_throws_for_unregistered_type(): void
    {
        $this->expectException(UnknownRuleTypeException::class);

        $this->manager->configurationFor(RuleType::MaxSizeRule);
    }

    public function test_create_returns_rule_instance(): void
    {
        $this->manager->register(RuleType::MaxSizeRule, new MaxSizeConfig());

        $rule = $this->manager->create(RuleType::MaxSizeRule, ['maxBytes' => 1024]);

        $this->assertInstanceOf(MaxSizeRuleImpl::class, $rule);
    }

    public function test_available_types_returns_registered_types(): void
    {
        $this->manager->register(RuleType::MaxSizeRule, new MaxSizeConfig());

        $types = $this->manager->availableTypes();

        $this->assertContains(RuleType::MaxSizeRule, $types);
    }

    public function test_available_types_returns_empty_when_nothing_registered(): void
    {
        $this->assertSame([], $this->manager->availableTypes());
    }

    public function test_schema_for_returns_expected_keys(): void
    {
        $this->manager->register(RuleType::MaxSizeRule, new MaxSizeConfig());

        $schema = $this->manager->schemaFor(RuleType::MaxSizeRule);

        $this->assertArrayHasKey(SchemaField::Type->value, $schema);
        $this->assertArrayHasKey(SchemaField::Label->value, $schema);
        $this->assertArrayHasKey(SchemaField::Description->value, $schema);
        $this->assertArrayHasKey(SchemaField::Parameters->value, $schema);
        $this->assertArrayHasKey(SchemaField::Defaults->value, $schema);
    }

    public function test_schema_for_type_value_matches_rule_type(): void
    {
        $this->manager->register(RuleType::MaxSizeRule, new MaxSizeConfig());

        $schema = $this->manager->schemaFor(RuleType::MaxSizeRule);

        $this->assertSame(RuleType::MaxSizeRule->value, $schema[SchemaField::Type->value]);
    }

    public function test_all_schemas_returns_schema_for_each_registered_type(): void
    {
        $this->manager->register(RuleType::MaxSizeRule, new MaxSizeConfig());
        $this->manager->register(RuleType::ProhibitedWordsRule, new ProhibitedWordsConfig());

        $schemas = $this->manager->allSchemas();

        $this->assertArrayHasKey(RuleType::MaxSizeRule->value, $schemas);
        $this->assertArrayHasKey(RuleType::ProhibitedWordsRule->value, $schemas);
    }

}
