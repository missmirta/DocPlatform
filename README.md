# DocPlatform

A multi-tenant document validation platform in pure PHP 8.1+. Each tenant manages its own set of validation rules; uploaded documents are checked against those rules and the results are stored per tenant.

## Setup

```bash
composer install
php seed.php               # creates runtime/tenants.sqlite3 and seeds tenant DBs
php -S localhost:8080 -t public
```

Open `http://localhost:8080` — you will be redirected to the rules list for the first tenant.

## Design Reasoning

Validation rules are modelled as interchangeable strategies, each implementing a single `ValidationRuleInterface::validate(Document): array` method. The validator runs every applicable rule and collects all errors — so a submitter sees every problem in one response rather than discovering violations one at a time. 

Adding a new rule type is a matter of creating one new class; no existing class needs to change. The `RuleConfigurationManager` acts as a self-describing registry of rule types: it knows how to instantiate each rule from its persisted parameters, which drives both the dynamic UI form fields and the runtime rule construction from the same source of truth.

Tenant isolation is enforced at the storage layer. A central `tenants.sqlite3` registry maps tenant IDs to their database paths; each tenant's rules and uploaded files live in a dedicated per-tenant SQLite file. This means one tenant's data is unreachable from another tenant's queries by construction rather than by convention. The `AppServiceProvider` resolves the correct database connection at request time using the tenant ID from the query string, keeping the bootstrap wiring in one place.

The HTTP layer is intentionally thin and framework-free. A hand-rolled `Router` handles parameterised path matching for the JSON API (`/api/tenants/{tenantId}/rules`) while the UI routes dispatch directly in `index.php`. Controllers are stateless — they receive their dependencies as constructor arguments — and delegate all logic to the service layer behind typed interfaces (`RuleServiceInterface`, `ValidatorServiceInterface`). This separation makes the service layer independently testable without bootstrapping HTTP machinery.

## Validation Rules

| Rule | Constructor params | What it checks |
|---|---|---|
| `MaxSizeRule` | `int $maxBytes` | `strlen($content) <= $maxBytes` |
| `MinContentLengthRule` | `int $minChars` | `strlen($content) >= $minChars` |
| `ProhibitedWordsRule` | `array $words` | None of `$words` appear in `$content` (case-insensitive; each violation reported separately) |
| `RequiredMetadataRule` | `array $fields` | All `$fields` exist as keys in `$metadata` (every missing field reported) |
| `AllowedContentTypeRule` | `array $types` | `$metadata['type']` is in `$types` (missing key → descriptive error, no exception) |
| `MetadataValueFormatRule` | `string $field, string $pattern` | `$metadata[$field]` matches regex `$pattern` (missing field → descriptive error, no exception) |

## Running Tests

```bash
./vendor/bin/phpunit
```