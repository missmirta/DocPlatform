<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DocPlatform\Bootstrap\AppServiceProvider;
use DocPlatform\Http\Controllers\RuleController;
use DocPlatform\Http\Controllers\TenantRuleController;
use DocPlatform\Http\Controllers\UploadFileController;
use DocPlatform\Http\Request;
use DocPlatform\Http\Router;

try {
    $registryPath = __DIR__ . '/../runtime/tenants.sqlite3';

    if (!file_exists($registryPath)) {
        http_response_code(503);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Setup required</title>'
            . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></head>'
            . '<body class="bg-light"><div class="container mt-5"><div class="alert alert-warning">'
            . '<h4 class="alert-heading">Database not found</h4>'
            . '<p>The tenant registry database does not exist yet.</p>'
            . '<p>Please run the following command from the project root:</p>'
            . '<pre class="bg-dark text-white p-3 rounded">php seed.php</pre>'
            . '</div></div></body></html>';
        exit;
    }

    $app = new AppServiceProvider($registryPath);

    $request = Request::fromGlobals();

    if (str_starts_with($request->path, '/api/')) {
        $controller = new TenantRuleController($app->ruleManagementService);
        $router = new Router();
        $router->get('/api/rule-types', [$controller, 'ruleTypes']);
        $router->get('/api/tenants/{tenantId}/rules', [$controller, 'index']);
        $router->post('/api/tenants/{tenantId}/rules', [$controller, 'store']);
        $router->put('/api/tenants/{tenantId}/rules/{ruleId}', [$controller, 'update']);
        $router->delete('/api/tenants/{tenantId}/rules/{ruleId}', [$controller, 'destroy']);
        $router->dispatch($request)->send();
        exit;
    }

    $tenantId = $_GET['tenant'] ?? $app->registry->listTenants()[0] ?? 'unknown';

    $method = $_SERVER['REQUEST_METHOD'];
    $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($method === 'GET' && $path === '/') {
        header('Location: /rules?tenant=' . urlencode($tenantId));
        exit;
    } elseif ($method === 'GET' && $path === '/rules') {
        (new RuleController())->index($tenantId, $app->ruleRepository, $app->configManager, $app->registry);
    } elseif ($method === 'GET' && $path === '/rules/create') {
        (new RuleController())->create($tenantId, $app->configManager, $app->registry);
    } elseif ($method === 'POST' && $path === '/rules/create') {
        (new RuleController())->store($_POST, $app->ruleRepository, $app->configManager, $app->registry);
    } elseif ($method === 'GET' && $path === '/rules/edit') {
        (new RuleController())->edit($tenantId, (int)($_GET['id'] ?? 0), $app->ruleRepository, $app->configManager, $app->registry);
    } elseif ($method === 'POST' && $path === '/rules/edit') {
        (new RuleController())->update($_POST, $app->ruleRepository, $app->configManager, $app->registry);
    } elseif ($method === 'POST' && $path === '/rules/delete') {
        (new RuleController())->destroy($_POST, $app->ruleRepository, $app->registry);
    } elseif ($method === 'GET' && $path === '/upload') {
        (new UploadFileController())->index($tenantId, $app->registry);
    } elseif ($method === 'POST' && $path === '/upload') {
        (new UploadFileController())->upload($_POST, $_FILES, $app->fileRepository, $app->ruleManagementService);
    } else {
        http_response_code(404);
        echo '404 Not Found';
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title>'
        . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></head>'
        . '<body class="bg-light"><div class="container mt-5"><div class="alert alert-danger">'
        . '<h4 class="alert-heading">Application Error</h4>'
        . '<p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>'
        . '<hr><pre class="small">' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>'
        . '</div></div></body></html>';
}
