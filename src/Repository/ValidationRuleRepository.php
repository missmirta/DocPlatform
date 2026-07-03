<?php

declare(strict_types=1);

namespace DocPlatform\Repository;

use DocPlatform\Exceptions\RuleNotFoundException;
use DocPlatform\Exceptions\UnknownTenantException;
use DocPlatform\Model\ValidationRule;
use DocPlatform\Repository\Contracts\TenantRepositoryInterface;
use DocPlatform\Repository\Contracts\ValidationRuleRepositoryInterface;
use DocPlatform\RuleConfiguration\RuleConfigurationManager;
use DocPlatform\RuleConfiguration\RuleType;
use JsonException;
use PDO;

final class ValidationRuleRepository implements ValidationRuleRepositoryInterface
{
    public function __construct(
        private readonly TenantRepositoryInterface $registry,
        private readonly RuleConfigurationManager $configManager,
    ) {}

    public function getRulesFor(string $tenantId): array
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare(
            'SELECT rule_type, parameters FROM validation_rules'
        );
        $stmt->execute([]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows === []) {
            throw new UnknownTenantException($tenantId);
        }

        return array_map(
            fn(array $row) => $this->configManager->create(
                RuleType::from($row['rule_type']),
                json_decode($row['parameters'], true, 512, JSON_THROW_ON_ERROR)
            ),
            $rows,
        );
    }

    public function existsRule(string $tenantId, RuleType $ruleType): bool
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM validation_rules WHERE rule_type = :type');
        $stmt->execute([':type' => $ruleType->value]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function addRule(string $tenantId, RuleType $ruleType, array $parameters): int
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare(
            'INSERT INTO validation_rules (rule_type, parameters) VALUES (:type, :params)'
        );
        $stmt->execute([
            ':type'   => $ruleType->value,
            ':params' => json_encode($parameters, JSON_THROW_ON_ERROR),
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updateRule(string $tenantId, int $ruleId, array $parameters): void
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare(
            'UPDATE validation_rules SET parameters = :params WHERE id = :id'
        );
        $stmt->execute([
            ':params' => json_encode($parameters, JSON_THROW_ON_ERROR),
            ':id'     => $ruleId,
        ]);
        if ($stmt->rowCount() === 0) {
            throw new RuleNotFoundException($ruleId);
        }
    }

    public function removeRule(string $tenantId, int $ruleId): void
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare('DELETE FROM validation_rules WHERE id = :id');
        $stmt->execute([':id' => $ruleId]);
        if ($stmt->rowCount() === 0) {
            throw new RuleNotFoundException($ruleId);
        }
    }

    /** @return ValidationRule[]
     * @throws JsonException
     */
    public function listRules(string $tenantId): array
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare('SELECT id, rule_type, parameters, sort_order FROM validation_rules ORDER BY sort_order');
        $stmt->execute([]);
        return array_map(
            fn(array $row) => new ValidationRule(
                (int) $row['id'],
                RuleType::from($row['rule_type']),
                json_decode($row['parameters'], true, 512, JSON_THROW_ON_ERROR),
                (int) $row['sort_order'],
            ),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    public function findById(string $tenantId, int $ruleId): ?ValidationRule
    {
        $pdo  = $this->registry->getConnectionFor($tenantId);
        $stmt = $pdo->prepare(
            'SELECT id, rule_type, parameters, sort_order FROM validation_rules WHERE id = :id'
        );
        $stmt->execute([':id' => $ruleId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return new ValidationRule(
            (int) $row['id'],
            RuleType::from($row['rule_type']),
            json_decode($row['parameters'], true, 512, JSON_THROW_ON_ERROR),
            (int) $row['sort_order'],
        );
    }
}
