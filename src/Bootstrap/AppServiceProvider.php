<?php

declare(strict_types=1);

namespace DocPlatform\Bootstrap;

use DocPlatform\Repository\Contracts\TenantRepositoryInterface;
use DocPlatform\Repository\Contracts\UploadedFileRepositoryInterface;
use DocPlatform\Repository\Contracts\ValidationRuleRepositoryInterface;
use DocPlatform\Repository\TenantRepository;
use DocPlatform\Repository\UploadedFileRepository;
use DocPlatform\Repository\ValidationRuleRepository;
use DocPlatform\Rule\Configurations\AllowedContentTypeRule;
use DocPlatform\Rule\Configurations\MaxSizeRule;
use DocPlatform\Rule\Configurations\MetadataValueFormatRule;
use DocPlatform\Rule\Configurations\ProhibitedWordsRule;
use DocPlatform\Rule\Configurations\RequiredMetadataRule;
use DocPlatform\Rule\Enum\RuleType;
use DocPlatform\Rule\RuleConfigurationManager;
use DocPlatform\Rule\RuleParamCaster;
use DocPlatform\Service\Contracts\ValidatorServiceInterface;
use DocPlatform\Service\RuleService;
use DocPlatform\Service\ValidatorService;
use PDO;

final class AppServiceProvider
{
    public readonly TenantRepositoryInterface $registry;
    public readonly RuleConfigurationManager $configManager;
    public readonly RuleParamCaster $paramCaster;
    public readonly ValidationRuleRepositoryInterface $ruleRepository;
    public readonly UploadedFileRepositoryInterface $fileRepository;
    public readonly ValidatorServiceInterface $validator;
    public readonly RuleService $ruleManagementService;

    public function __construct(string $registryPath)
    {
        $pdo = new PDO('sqlite:' . $registryPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->bootRuleTypes();
        $this->paramCaster = new RuleParamCaster($this->configManager);

        $this->registry = new TenantRepository($pdo);
        $this->ruleRepository = new ValidationRuleRepository($this->registry, $this->configManager);
        $this->fileRepository = new UploadedFileRepository($this->registry);
        $this->validator = new ValidatorService();
        $this->ruleManagementService = new RuleService($this->ruleRepository, $this->configManager, $this->validator);
    }

    private function bootRuleTypes(): void
    {
        $this->configManager = new RuleConfigurationManager();
        $this->configManager->register(RuleType::MaxSizeRule,             new MaxSizeRule());
        $this->configManager->register(RuleType::ProhibitedWordsRule,     new ProhibitedWordsRule());
        $this->configManager->register(RuleType::RequiredMetadataRule,    new RequiredMetadataRule());
        $this->configManager->register(RuleType::AllowedContentTypeRule,  new AllowedContentTypeRule());
        $this->configManager->register(RuleType::MetadataValueFormatRule, new MetadataValueFormatRule());
    }
}
