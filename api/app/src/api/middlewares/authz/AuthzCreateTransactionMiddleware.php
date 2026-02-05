<?php

namespace api\middlewares\authz;

use application_core\exceptions\InsufficientRightsAuthzException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class AuthzCreateTransactionMiddleware {

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

        $body = $request->getParsedBody();
        if (!is_array($body)) {
            throw new HttpBadRequestException($request, 'Body JSON invalide.');
        }

        $id_emetteur = $body['id_emetteur'];
        $id_recepteur = $body['id_recepteur'];

        if (!$id_emetteur || !$id_recepteur) {
            throw new HttpBadRequestException($request, 'Données de transaction manquantes dans le corps de la requête');
        }

        if($id !== $id_emetteur) {
            throw new InsufficientRightsAuthzException("Niveau d'accès insuffisant");
        }

        return $next->handle($request);
    }
}