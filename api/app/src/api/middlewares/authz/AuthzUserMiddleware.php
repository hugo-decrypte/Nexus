<?php

namespace api\middlewares\authz;

use application_core\application\usecases\AuthzUserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteContext;

class AuthzUserMiddleware {
    private AuthzUserService $authzUser;
    private array $operations;

    public function __construct(AuthzUserService $authzUser, array $operations = [AuthzUserService::ROLE_CLIENT]) {
        $this->authzUser = $authzUser;
        $this->operations = $operations;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $payload = $request->getAttribute('user_payload');

        if (!$payload) {
            throw new HttpUnauthorizedException($request, "Non authentifié");
        }

        $routeContext = RouteContext::fromRequest($request);
        $ressourceId = $routeContext->getRoute()->getArgument('id');

        $granted = false;
        $lastException = null;

        foreach ($this->operations as $op) {
            try {
//                $this->authzUser->isGranted($payload->data->id, $payload->role, $ressourceId, $op);
                $this->authzUser->isGranted($payload->data->role, $op);

                $granted = true;
                break;
            } catch (\Exception $e) {
                $lastException = $e;
            }
        }

        if (!$granted) {
            throw new HttpForbiddenException($request, "Accès refusé : " . ($lastException ? $lastException->getMessage() : "Droits insuffisants"));
        }

        return $handler->handle($request);
    }
}