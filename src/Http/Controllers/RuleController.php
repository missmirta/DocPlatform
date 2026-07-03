<?php

declare(strict_types=1);

namespace DocPlatform\Http\Controllers;

use DocPlatform\Model\ValidationRule;
use DocPlatform\Repository\Contracts\TenantRepositoryInterface;
use DocPlatform\Repository\Contracts\ValidationRuleRepositoryInterface;
use DocPlatform\RuleConfiguration\RuleConfigurationManager;
use DocPlatform\RuleConfiguration\RuleType;

// TODO: RuleController renders HTML templates for the browser UI (GET /rules, POST /rules/create, etc.)
// So, it is needed just for demo.
// REST API logic is in TenantRuleController

class RuleController
{
    public function index(
        string $tenantId,
        ValidationRuleRepositoryInterface $ruleRepo,
        RuleConfigurationManager $schemaManager,
        TenantRepositoryInterface $registry,
    ): void {
        $rules = $ruleRepo->listRules($tenantId);
        $schemas = $schemaManager->allSchemas();
        $tenants = $registry->listTenants();

        $this->render('rules/index', [
            'tenantId' => $tenantId,
            'rules'    => $rules,
            'schemas'  => $schemas,
            'tenants'  => $tenants,
        ]);
    }

    public function create(
        string $tenantId,
        RuleConfigurationManager $schemaManager,
        TenantRepositoryInterface $registry,
    ): void {
        $schemas = $schemaManager->allSchemas();
        $tenants = $registry->listTenants();

        $this->render('rules/create', [
            'tenantId' => $tenantId,
            'schemas'  => $schemas,
            'tenants'  => $tenants,
            'errors'   => [],
            'old'      => [],
        ]);
    }

    public function store(
        array $post,
        ValidationRuleRepositoryInterface $ruleRepo,
        RuleConfigurationManager $schemaManager,
        TenantRepositoryInterface $registry,
    ): void {
        $tenantId = $post['tenant'] ?? '';
        $ruleType = RuleType::from($post['rule_type'] ?? '');
        $schemas = $schemaManager->allSchemas();
        $tenants = $registry->listTenants();

        $rawParams = $post['params'] ?? [];
        $params = $schemaManager->buildFormParams($rawParams, $ruleType);

        $errors = $schemaManager->configurationFor($ruleType)->validate($params);

        if (empty($errors) && $ruleRepo->existsRule($tenantId, $ruleType)) {
            $errors[] = "A rule of type '{$ruleType->value}' already exists for this tenant.";
        }

        if (!empty($errors)) {
            $this->render('rules/create', [
                'tenantId' => $tenantId,
                'schemas'  => $schemas,
                'tenants'  => $tenants,
                'errors'   => $errors,
                'old'      => $post,
            ]);
            return;
        }

        $ruleRepo->addRule($tenantId, $ruleType, $params);

        header('Location: /rules?tenant=' . urlencode($tenantId) . '&flash=Rule+added');
        exit;
    }

    public function edit(
        string $tenantId,
        int $ruleId,
        ValidationRuleRepositoryInterface $ruleRepo,
        RuleConfigurationManager $schemaManager,
        TenantRepositoryInterface $registry,
    ): void {
        $rule = $ruleRepo->findById($tenantId, $ruleId);

        if ($rule === null) {
            header('Location: /rules?tenant=' . urlencode($tenantId) . '&flash=Rule+not+found');
            exit;
        }

        $schemas = $schemaManager->allSchemas();
        $tenants = $registry->listTenants();
        $ruleType = $rule->ruleType;

        $displayParams = $schemaManager->prepareDisplayParams($rule->parameters, $ruleType);

        $this->render('rules/edit', [
            'tenantId'      => $tenantId,
            'rule'          => $rule,
            'schemas'       => $schemas,
            'tenants'       => $tenants,
            'errors'        => [],
            'displayParams' => $displayParams,
        ]);
    }

    public function update(
        array $post,
        ValidationRuleRepositoryInterface $ruleRepo,
        RuleConfigurationManager $schemaManager,
        TenantRepositoryInterface $registry,
    ): void {
        $tenantId = $post['tenant'] ?? '';
        $ruleId = (int)($post['rule_id'] ?? 0);
        $ruleType = RuleType::from($post['rule_type'] ?? '');
        $schemas = $schemaManager->allSchemas();
        $tenants = $registry->listTenants();

        $rawParams = $post['params'] ?? [];
        $params = $schemaManager->buildFormParams($rawParams, $ruleType);

        $errors = $schemaManager->configurationFor($ruleType)->validate($params);

        if (!empty($errors)) {
            $rule = $ruleRepo->findById($tenantId, $ruleId);

            $displayParams = $schemaManager->prepareDisplayParams($params, $ruleType);

            $this->render('rules/edit', [
                'tenantId'      => $tenantId,
                'rule'          => $rule ?? new ValidationRule($ruleId, $ruleType, $params, 0),
                'schemas'       => $schemas,
                'tenants'       => $tenants,
                'errors'        => $errors,
                'displayParams' => $displayParams,
            ]);
            return;
        }

        $ruleRepo->updateRule($tenantId, $ruleId, $params);

        header('Location: /rules?tenant=' . urlencode($tenantId) . '&flash=Rule+updated');
        exit;
    }

    public function destroy(
        array $post,
        ValidationRuleRepositoryInterface $ruleRepo,
        TenantRepositoryInterface $registry,
    ): void {
        $tenantId = $post['tenant'] ?? '';
        $ruleId = (int)($post['rule_id'] ?? 0);

        $ruleRepo->removeRule($tenantId, $ruleId);

        header('Location: /rules?tenant=' . urlencode($tenantId) . '&flash=Rule+deleted');
        exit;
    }

    private function render(string $template, array $vars): void
    {
        extract($vars);
        include __DIR__ . '/../../../templates/' . $template . '.php';
    }
}
