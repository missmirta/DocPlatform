<?php

declare(strict_types=1);

namespace DocPlatform\Http\Controllers;

use DocPlatform\Exceptions\DuplicateRuleTypeException;
use DocPlatform\Exceptions\InvalidRuleParametersException;
use DocPlatform\Exceptions\RuleNotFoundException;
use DocPlatform\Exceptions\UnknownRuleTypeException;
use DocPlatform\Http\Request;
use DocPlatform\Http\Requests\StoreRuleRequest;
use DocPlatform\Http\Response;
use DocPlatform\Service\Contracts\RuleServiceInterface;

final class TenantRuleController
{
    public function __construct(
        private readonly RuleServiceInterface $service,
    ) {}

    public function index(Request $request, string $tenantId): Response
    {
        return Response::json(['data' => $this->service->index($tenantId)]);
    }

    public function store(Request $request, string $tenantId): Response
    {
        try {
            $storeRequest = StoreRuleRequest::fromBody($request->body);
            $id = $this->service->add(
                $tenantId,
                $storeRequest->ruleType,
                $storeRequest->parameters,
            );
            return Response::json(['id' => $id], 201);
        } catch (DuplicateRuleTypeException $e) {
            return Response::json(['error' => $e->getMessage()], 409);
        } catch (InvalidRuleParametersException $e) {
            return Response::json(['errors' => $e->getErrors()], 422);
        } catch (UnknownRuleTypeException $e) {
            return Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, string $tenantId, string $ruleId): Response
    {
        try {
            $this->service->update($tenantId, (int) $ruleId, $request->body['parameters'] ?? []);
            return Response::json([], 204);
        } catch (RuleNotFoundException $e) {
            return Response::json(['error' => $e->getMessage()], 404);
        } catch (InvalidRuleParametersException $e) {
            return Response::json(['errors' => $e->getErrors()], 422);
        }
    }

    public function destroy(Request $request, string $tenantId, string $ruleId): Response
    {
        try {
            $this->service->remove($tenantId, (int) $ruleId);
            return Response::json([], 204);
        } catch (RuleNotFoundException $e) {
            return Response::json(['error' => $e->getMessage()], 404);
        }
    }

    public function ruleTypes(Request $request): Response
    {
        return Response::json(['data' => $this->service->availableRuleTypes()]);
    }
}
