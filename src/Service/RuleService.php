<?php

declare(strict_types=1);

namespace DocPlatform\Service;

use DocPlatform\Exceptions\RuleNotFoundException;
use DocPlatform\Model\Document;
use DocPlatform\Repository\Contracts\ValidationRuleRepositoryInterface;
use DocPlatform\RuleConfiguration\RuleConfigurationManager;
use DocPlatform\RuleConfiguration\RuleType;
use DocPlatform\Service\Contracts\RuleServiceInterface;
use DocPlatform\Service\Contracts\ValidatorServiceInterface;

final class RuleService implements RuleServiceInterface
{
    public function __construct(
        private readonly ValidationRuleRepositoryInterface $repository,
        private readonly RuleConfigurationManager $configManager,
        private readonly ValidatorServiceInterface $validator,
    ) {}

    public function add(string $tenantId, RuleType $ruleType, array $parameters): int
    {
        $validated = $this->configManager
            ->configurationFor($ruleType)
            ->validateAndMergeDefaults($parameters);

        return $this->repository->addRule($tenantId, $ruleType, $validated);
    }

    public function update(string $tenantId, int $ruleId, array $parameters): void
    {
        $existing = $this->repository->findById($tenantId, $ruleId)
            ?? throw new RuleNotFoundException($ruleId);

        $validated = $this->configManager
            ->configurationFor($existing->ruleType)
            ->validateAndMergeDefaults($parameters);

        $this->repository->updateRule($tenantId, $ruleId, $validated);
    }

    public function remove(string $tenantId, int $ruleId): void
    {
        $this->repository->findById($tenantId, $ruleId)
            ?? throw new RuleNotFoundException($ruleId);

        $this->repository->removeRule($tenantId, $ruleId);
    }

    public function index(string $tenantId): array
    {
        return $this->repository->listRules($tenantId);
    }

    public function availableRuleTypes(): array
    {
        return $this->configManager->allSchemas();
    }

    /** @return string[] */
    public function validate(string $tenantId, Document $document): array
    {
        return $this->validator->errors($document, $this->repository->getRulesFor($tenantId));
    }
}
