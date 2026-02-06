<?php

namespace api\middlewares\authz;

use application_core\exceptions\InsufficientRightsAuthzException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteContext;

class AuthzUserRessourceAccessMiddleware {

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
        $id_user = $route->getArgument('id_user');

        if (!$id_user) {
            throw new HttpInternalServerErrorException($request, 'Paramètre de route manquant');
        }

        if($id !== $id_user) {
            throw new InsufficientRightsAuthzException("Niveau d'accès insuffisant");
        }

        return $next->handle($request);
    }
}