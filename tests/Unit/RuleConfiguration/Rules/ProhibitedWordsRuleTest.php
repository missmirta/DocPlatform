<?php

declare(strict_types=1);

namespace Tests\Unit\RuleConfiguration\Rules;

use DocPlatform\Model\Document;
use DocPlatform\RuleConfiguration\Rules\ProhibitedWordsRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProhibitedWordsRule::class)]
final class ProhibitedWordsRuleTest extends TestCase
{
    private function doc(string $textContent): Document
    {
        return new Document('id', '', $textContent, [], '2024-01-01');
    }

    public function test_validate_passes_when_no_prohibited_word_present(): void
    {
        $rule = new ProhibitedWordsRule(['spam', 'junk']);

        $this->assertSame([], $rule->validate($this->doc('clean content here')));
    }

    public function test_validate_fails_when_prohibited_word_found(): void
    {
        $rule = new ProhibitedWordsRule(['spam']);

        $errors = $rule->validate($this->doc('this is spam content'));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('spam', $errors[0]);
    }

    public function test_validate_is_case_insensitive(): void
    {
        $rule = new ProhibitedWordsRule(['spam']);

        $errors = $rule->validate($this->doc('This is SPAM'));

        $this->assertCount(1, $errors);
    }

    public function test_validate_reports_each_found_prohibited_word(): void
    {
        $rule = new ProhibitedWordsRule(['spam', 'junk']);

        $errors = $rule->validate($this->doc('spam and junk content'));

        $this->assertCount(2, $errors);
    }

    public function test_validate_does_not_report_absent_words(): void
    {
        $rule = new ProhibitedWordsRule(['spam', 'junk']);

        $errors = $rule->validate($this->doc('only spam here'));

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('spam', $errors[0]);
    }

    public function test_validate_passes_for_empty_text_content(): void
    {
        $rule = new ProhibitedWordsRule(['spam']);

        $this->assertSame([], $rule->validate($this->doc('')));
    }
}
