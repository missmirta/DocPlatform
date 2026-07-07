<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DocPlatform\Model\Document;
use DocPlatform\Rule\Configurations\MaxSizeRule;
use DocPlatform\Rule\Configurations\ProhibitedWordsRule;
use DocPlatform\Rule\Configurations\RequiredMetadataRule;
use DocPlatform\Rule\TenantRuleProvider;
use DocPlatform\Service\ValidatorService;

// 1. Build rule instances for each tenant using configuration objects
$provider = new TenantRuleProvider();

$maxSizeConfig      = new MaxSizeRule();
$requiredMetaConfig = new RequiredMetadataRule();
$prohibitedConfig   = new ProhibitedWordsRule();

$provider->addRule('tenant-acme', $maxSizeConfig->create(['maxBytes' => 1000]));
$provider->addRule('tenant-acme', $requiredMetaConfig->create(['fields' => ['author', 'type']]));
$provider->addRule('tenant-acme', $prohibitedConfig->create(['words' => ['confidential', 'secret']]));

$provider->addRule('tenant-beta', $maxSizeConfig->create(['maxBytes' => 500]));
$provider->addRule('tenant-beta', $requiredMetaConfig->create(['fields' => ['title']]));

// 2. Create the validator
$validator = new ValidatorService();

// 3. Validate a document that passes all rules
$passing = new Document(
    id:          'doc-001',
    tenantId:    'tenant-acme',
    content:     'This is a normal business document.',
    textContent: 'This is a normal business document.',
    metadata:    ['author' => 'Alice', 'type' => 'pdf'],
    uploadedAt:  '2024-01-01T00:00:00Z',
);

$result = $validator->validate($passing, ...$provider->getRulesForTenant('tenant-acme'));
echo 'doc-001 (tenant-acme): ' . ($result->isValid ? 'PASSED' : 'FAILED — ' . implode('; ', $result->errors)) . PHP_EOL;

// 4. Validate a document that fails multiple rules
$failing = new Document(
    id:          'doc-002',
    tenantId:    'tenant-acme',
    content:     'This is confidential information.',
    textContent: 'This is confidential information.',
    metadata:    ['type' => 'pdf'],
    uploadedAt:  '2024-01-01T00:00:00Z',
);

$result = $validator->validate($failing, ...$provider->getRulesForTenant('tenant-acme'));
echo 'doc-002 (tenant-acme): ' . ($result->isValid ? 'PASSED' : 'FAILED — ' . implode('; ', $result->errors)) . PHP_EOL;

// 5. Validate against a different tenant's rules
$betaDoc = new Document(
    id:          'doc-003',
    tenantId:    'tenant-beta',
    content:     'Short doc.',
    textContent: 'Short doc.',
    metadata:    ['title' => 'Q1 Report'],
    uploadedAt:  '2024-01-01T00:00:00Z',
);

$result = $validator->validate($betaDoc, ...$provider->getRulesForTenant('tenant-beta'));
echo 'doc-003 (tenant-beta): ' . ($result->isValid ? 'PASSED' : 'FAILED — ' . implode('; ', $result->errors)) . PHP_EOL;
