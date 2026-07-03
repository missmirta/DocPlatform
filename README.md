# DocPlatform

A multi-tenant document validation platform in pure PHP 8.1+. Each tenant manages its own set of validation rules; uploaded documents are checked against those rules and the results are stored per tenant.

## Quick Demo

Short integration script that demonstrates:
- Creating several validation rules
- Creating a validator
- Determining which validation rules apply for a given tenant ID
- Validating a document using those rules
- Handling both success and validation errors

```bash
php integration.php
```

## Design Reasoning

Validation rules are modelled as interchangeable strategies, each implementing `ValidationRuleInterface::validate(Document): string[]`. The `ValidatorService` runs every applicable rule, merges all errors, and returns a single `ValidationResult` — so a caller sees every violation in one response rather than discovering them one at a time.

Adding a new rule type requires two small classes: a rule implementation (`ValidationRuleInterface`) and a configuration (`AbstractRuleConfiguration` subclass). No existing class changes. The `RuleConfigurationManager` acts as a self-describing registry: it knows how to instantiate each rule from its parameters and how to expose the parameter schema — one source of truth for both validation and introspection.

Rule configurations validate their own parameters before constructing a rule instance, throwing `InvalidRuleParametersException` with all errors collected. This keeps invalid configuration from propagating silently into the validation pipeline.

## Validation Rules

| Rule | Constructor params | What it checks |
|---|---|---|
| `MaxSizeRule` | `int $maxBytes` | `strlen($content) <= $maxBytes` |
| `ProhibitedWordsRule` | `array $words` | None of `$words` appear in `$textContent` (case-insensitive; each violation reported separately) |
| `RequiredMetadataRule` | `array $fields` | All `$fields` exist as keys in `$metadata` (every missing field reported) |
| `AllowedContentTypeRule` | `array $types` | `$metadata['type']` is in `$types` (missing key → descriptive error, no exception) |
| `MetadataValueFormatRule` | `string $field, string $pattern` | `$metadata[$field]` matches regex `$pattern` (missing field → descriptive error, no exception) |

## Running Tests

```bash
./vendor/bin/phpunit
```