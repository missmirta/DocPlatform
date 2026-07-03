<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DocPlatform\Model\Document;
use DocPlatform\Rule\Configurations\MaxSizeRule;
use DocPlatform\Rule\Configurations\ProhibitedWordsRule;
use DocPlatform\Rule\Configurations\RequiredMetadataRule;
use DocPlatform\Rule\Enum\RuleType;
use DocPlatform\Rule\RuleConfigurationManager;
use DocPlatform\Service\ValidatorService;

// According to Test task:
//Provide a short integration script that demonstrates:
//–	Creating several validation rules
//–	Creating a validator
//–	Determining which validation rules apply for a given tenant ID
//–	Validating a document using those rules
//–	Handling both success and validation errors

// 1. Register available rule types
$manager = new RuleConfigurationManager();
$manager->register(RuleType::MaxSizeRule,          new MaxSizeRule());
$manager->register(RuleType::RequiredMetadataRule, new RequiredMetadataRule());
$manager->register(RuleType::ProhibitedWordsRule,  new ProhibitedWordsRule());

// 2. Define which rules apply per tenant
$tenantRules = [
    'tenant-acme' => [
        $manager->create(RuleType::MaxSizeRule,          ['maxBytes' => 1000]),
        $manager->create(RuleType::RequiredMetadataRule, ['fields' => ['author', 'type']]),
        $manager->create(RuleType::ProhibitedWordsRule,  ['words' => ['confidential', 'secret']]),
    ],
    'tenant-beta' => [
        $manager->create(RuleType::MaxSizeRule,          ['maxBytes' => 500]),
        $manager->create(RuleType::RequiredMetadataRule, ['fields' => ['title']]),
    ],
];

// 3. Create the validator
$validator = new ValidatorService();

// 4. Validate a document that passes all rules
$passing = new Document(
    id:          'doc-001',
    tenantId:    'tenant-acme',
    content:     'This is a normal business document.',
    textContent: 'This is a normal business document.',
    metadata:    ['author' => 'Alice', 'type' => 'pdf'],
    uploadedAt:  '2024-01-01T00:00:00Z',
);

$result = $validator->validate($passing, $tenantRules['tenant-acme']);
echo 'doc-001 (tenant-acme): ' . ($result->isValid ? 'PASSED' : 'FAILED — ' . implode('; ', $result->errors)) . PHP_EOL;

// 5. Validate a document that fails multiple rules
$failing = new Document(
    id:          'doc-002',
    tenantId:    'tenant-acme',
    content:     'This is confidential information.',
    textContent: 'This is confidential information.',
    metadata:    ['type' => 'pdf'],
    uploadedAt:  '2024-01-01T00:00:00Z',
);

$result = $validator->validate($failing, $tenantRules['tenant-acme']);
echo 'doc-002 (tenant-acme): ' . ($result->isValid ? 'PASSED' : 'FAILED — ' . implode('; ', $result->errors)) . PHP_EOL;

// 6. Validate against a different tenant's rules
$betaDoc = new Document(
    id:          'doc-003',
    tenantId:    'tenant-beta',
    content:     'Short doc.',
    textContent: 'Short doc.',
    metadata:    ['title' => 'Q1 Report'],
    uploadedAt:  '2024-01-01T00:00:00Z',
);

$result = $validator->validate($betaDoc, $tenantRules['tenant-beta']);
echo 'doc-003 (tenant-beta): ' . ($result->isValid ? 'PASSED' : 'FAILED — ' . implode('; ', $result->errors)) . PHP_EOL;
