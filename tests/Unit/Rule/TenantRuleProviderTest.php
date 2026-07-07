<?php

declare(strict_types=1);

namespace Tests\Unit\Rule;

use DocPlatform\Model\Document;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;
use DocPlatform\Rule\TenantRuleProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TenantRuleProvider::class)]
final class TenantRuleProviderTest extends TestCase
{
    private TenantRuleProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new TenantRuleProvider();
    }

    private function rule(): ValidationRuleInterface
    {
        return new class implements ValidationRuleInterface {
            public function validate(Document $document): array { return []; }
        };
    }

    public function test_get_rules_for_tenant_returns_empty_array_for_unknown_tenant(): void
    {
        $this->assertSame([], $this->provider->getRulesForTenant('unknown'));
    }

    public function test_add_rule_makes_rule_available_for_tenant(): void
    {
        $rule = $this->rule();
        $this->provider->addRule('tenant-a', $rule);

        $this->assertContains($rule, $this->provider->getRulesForTenant('tenant-a'));
    }

    public function test_add_multiple_rules_for_same_tenant(): void
    {
        $rule1 = $this->rule();
        $rule2 = $this->rule();
        $this->provider->addRule('tenant-a', $rule1);
        $this->provider->addRule('tenant-a', $rule2);

        $rules = $this->provider->getRulesForTenant('tenant-a');

        $this->assertCount(2, $rules);
        $this->assertContains($rule1, $rules);
        $this->assertContains($rule2, $rules);
    }

    public function test_rules_are_isolated_between_tenants(): void
    {
        $ruleA = $this->rule();
        $ruleB = $this->rule();
        $this->provider->addRule('tenant-a', $ruleA);
        $this->provider->addRule('tenant-b', $ruleB);

        $this->assertNotContains($ruleA, $this->provider->getRulesForTenant('tenant-b'));
        $this->assertNotContains($ruleB, $this->provider->getRulesForTenant('tenant-a'));
    }

    public function test_get_rules_returns_rules_in_insertion_order(): void
    {
        $rule1 = $this->rule();
        $rule2 = $this->rule();
        $rule3 = $this->rule();
        $this->provider->addRule('tenant-a', $rule1);
        $this->provider->addRule('tenant-a', $rule2);
        $this->provider->addRule('tenant-a', $rule3);

        $this->assertSame([$rule1, $rule2, $rule3], $this->provider->getRulesForTenant('tenant-a'));
    }
}
