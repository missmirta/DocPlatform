<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

if (!is_dir(__DIR__ . '/runtime')) {
    mkdir(__DIR__ . '/runtime', 0755, true);
}

$registryPdo = new PDO('sqlite:' . __DIR__ . '/runtime/tenants.sqlite3');
$registryPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$registryPdo->exec(file_get_contents(__DIR__ . '/migrations/002_create_tenants_registry.sql'));
$registryPdo->exec('DELETE FROM tenants');

$tenantDefs = [
    'tenant-acme' => [
        ['MaxSizeRule',             '{"maxBytes":1000}',                                                 0],
        ['RequiredMetadataRule',    '{"fields":["author","type"]}',                                      2],
        ['AllowedContentTypeRule',  '{"types":["pdf","docx"]}',                                          3],
        ['ProhibitedWordsRule',     '{"words":["confidential","secret"]}',                               4],
        ['MetadataValueFormatRule', json_encode(['field' => 'reference', 'pattern' => '/^REF-\d{4}$/']), 5],
    ],
    'tenant-beta' => [
        ['MaxSizeRule',          '{"maxBytes":500}',     0],
        ['RequiredMetadataRule', '{"fields":["title"]}', 1],
    ],
];

$regStmt = $registryPdo->prepare('INSERT INTO tenants (tenant_id, db_path) VALUES (?, ?)');

foreach ($tenantDefs as $tenantId => $rules) {
    $dbPath    = __DIR__ . '/runtime/' . $tenantId . '.sqlite3';
    $tenantPdo = new PDO('sqlite:' . $dbPath);
    $tenantPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $tenantPdo->exec(file_get_contents(__DIR__ . '/migrations/001_create_validation_rules.sql'));
    $tenantPdo->exec(file_get_contents(__DIR__ . '/migrations/003_create_uploaded_files.sql'));
    $tenantPdo->exec('DELETE FROM validation_rules');
    $tenantPdo->exec('DELETE FROM uploaded_files');

    $stmt = $tenantPdo->prepare(
        'INSERT INTO validation_rules (rule_type, parameters, sort_order) VALUES (?, ?, ?)'
    );
    foreach ($rules as [$type, $params, $order]) {
        $stmt->execute([$type, $params, $order]);
    }

    $regStmt->execute([$tenantId, $dbPath]);
    echo "Seeded: {$tenantId}\n";
}

echo "Done.\n";
