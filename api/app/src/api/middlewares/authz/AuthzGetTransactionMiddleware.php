<?php

namespace api\middlewares\authz;

use application_core\exceptions\InsufficientRightsAuthzException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteContext;

class AuthzGetTransactionMiddleware {

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        $payload = $request->getAttribute('user_payload');

        if (!$payload) {
            throw new HttpUnauthorizedException($request, "Non authentifié");
        }

        $id = $payload->sub;
        $role = $payload->data->role;

        if ($role === 'admin') {
            return $next->handle($request);
        }

        $route = RouteContext::fromRequest($request)->getRoute();
        $id_emetteur = $route->getArgument('id_emetteur');
        $id_recepteur = $route->getArgument('id_recepteur');

        if (!$id_emetteur || !$id_recepteur) {
            throw new HttpInternalServerErrorException($request, 'Paramètres de route manquants');
        }

        if(($id !== $id_emetteur) || ($id !== $id_recepteur)) {
            throw new InsufficientRightsAuthzException("Niveau d'accès insuffisant");
        }

        return $next->handle($request);
    }
}