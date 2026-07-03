<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Contracts\ValidationRuleInterface;
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
        $this->document = new Document('id', 'content', 'text', [], '2024-01-01');
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

    public function test_errors_returns_empty_when_no_rules_provided(): void
    {
        $this->assertSame([], $this->service->errors($this->document, []));
    }

    public function test_errors_returns_empty_when_all_rules_pass(): void
    {
        $errors = $this->service->errors($this->document, [
            $this->passingRule(),
            $this->passingRule(),
        ]);

        $this->assertSame([], $errors);
    }

    public function test_errors_returns_messages_from_failing_rule(): void
    {
        $errors = $this->service->errors($this->document, [
            $this->failingRule('error one'),
        ]);

        $this->assertSame(['error one'], $errors);
    }

    public function test_errors_merges_messages_from_multiple_failing_rules(): void
    {
        $errors = $this->service->errors($this->document, [
            $this->failingRule('error A'),
            $this->failingRule('error B'),
        ]);

        $this->assertSame(['error A', 'error B'], $errors);
    }

    public function test_errors_collects_multiple_messages_from_one_rule(): void
    {
        $errors = $this->service->errors($this->document, [
            $this->failingRule('error 1', 'error 2'),
        ]);

        $this->assertSame(['error 1', 'error 2'], $errors);
    }

    public function test_is_valid_returns_true_when_no_rules_provided(): void
    {
        $this->assertTrue($this->service->isValid($this->document, []));
    }

    public function test_is_valid_returns_true_when_all_rules_pass(): void
    {
        $this->assertTrue($this->service->isValid($this->document, [$this->passingRule()]));
    }

    public function test_is_valid_returns_false_when_any_rule_fails(): void
    {
        $this->assertFalse($this->service->isValid($this->document, [
            $this->passingRule(),
            $this->failingRule('something wrong'),
        ]));
    }
}
