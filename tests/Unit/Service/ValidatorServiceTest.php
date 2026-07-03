<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use DocPlatform\Model\Document;
use DocPlatform\Rule\Contracts\ValidationRuleInterface;
use DocPlatform\Service\ValidatorService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidatorService::class)]
final class ValidatorServiceTest extends TestCase
{
    private ValidatorService $service;
    private Document $document;

    protected function setUp(): void
    {
        $this->service = new ValidatorService();
        $this->document = new Document('id', 'tenant', 'content', 'text', [], '2024-01-01');
    }

    private function passingRule(): ValidationRuleInterface
    {
        return new class implements ValidationRuleInterface {
            public function validate(Document $document): array { return []; }
        };
    }

    private function failingRule(string ...$messages): ValidationRuleInterface
    {
        return new class($messages) implements ValidationRuleInterface {
            public function __construct(private readonly array $messages) {}
            public function validate(Document $document): array { return $this->messages; }
        };
    }

    public function test_validate_returns_empty_errors_when_no_rules_provided(): void
    {
        $result = $this->service->validate($this->document, []);

        $this->assertSame([], $result->errors);
    }

    public function test_validate_returns_empty_errors_when_all_rules_pass(): void
    {
        $result = $this->service->validate($this->document, [
            $this->passingRule(),
            $this->passingRule(),
        ]);

        $this->assertSame([], $result->errors);
    }

    public function test_validate_returns_messages_from_failing_rule(): void
    {
        $result = $this->service->validate($this->document, [
            $this->failingRule('error one'),
        ]);

        $this->assertSame(['error one'], $result->errors);
    }

    public function test_validate_merges_messages_from_multiple_failing_rules(): void
    {
        $result = $this->service->validate($this->document, [
            $this->failingRule('error A'),
            $this->failingRule('error B'),
        ]);

        $this->assertSame(['error A', 'error B'], $result->errors);
    }

    public function test_validate_collects_multiple_messages_from_one_rule(): void
    {
        $result = $this->service->validate($this->document, [
            $this->failingRule('error 1', 'error 2'),
        ]);

        $this->assertSame(['error 1', 'error 2'], $result->errors);
    }

    public function test_is_valid_true_when_no_rules_provided(): void
    {
        $result = $this->service->validate($this->document, []);

        $this->assertTrue($result->isValid);
    }

    public function test_is_valid_true_when_all_rules_pass(): void
    {
        $result = $this->service->validate($this->document, [$this->passingRule()]);

        $this->assertTrue($result->isValid);
    }

    public function test_is_valid_false_when_any_rule_fails(): void
    {
        $result = $this->service->validate($this->document, [
            $this->passingRule(),
            $this->failingRule('something wrong'),
        ]);

        $this->assertFalse($result->isValid);
    }
}
