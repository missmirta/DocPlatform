<?php

declare(strict_types=1);

namespace DocPlatform\Http\Controllers;

use DocPlatform\Http\Requests\UploadRequest;
use DocPlatform\Model\Document;
use DocPlatform\Repository\Contracts\TenantRepositoryInterface;
use DocPlatform\Repository\Contracts\UploadedFileRepositoryInterface;
use DocPlatform\Service\Contracts\RuleServiceInterface;

class UploadFileController
{
    // When auth lands, it becomes redundant and can be removed.
    public function index(
        string $tenantId, // TODO: maybe be taken from global context 'tenant-id' auth response.
        TenantRepositoryInterface $registry,
    ): void {
        $this->render('upload/index', $this->indexViewData($tenantId, $registry));
    }

    public function upload(
        array $post,
        array $files,
        UploadedFileRepositoryInterface $fileRepo,
        RuleServiceInterface $ruleService,
    ): void {
        $params = UploadRequest::fromRaw($post, $files);

        $docId = 'doc-' . bin2hex(random_bytes(8));
        $doc = new Document($docId, $params->tenantId, $params->content, $params->textContent, $params->metadata, date('c'));

        $result = $ruleService->validate($params->tenantId, $doc);

        if ($result->isValid) {
            $fileRepo->save($params->tenantId, $doc);
        }

        $this->render('upload/result', [
            'errors'   => $result->errors,
            'doc'      => $doc,
            'tenantId' => $params->tenantId,
            'saved'    => $result->isValid,
        ]);
    }

    private function indexViewData(
        string $tenantId,
        TenantRepositoryInterface $registry,
    ): array {
        return [
            'tenantId' => $tenantId,
            'tenants'  => $registry->listTenants(),
        ];
    }

    private function render(string $template, array $vars): void
    {
        extract($vars);
        include __DIR__ . '/../../../templates/' . $template . '.php';
    }
}
