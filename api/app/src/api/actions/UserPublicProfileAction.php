<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

/**
 * Profil public (id, prénom, nom) pour afficher le destinataire après scan QR.
 * Protégé par JWT : seul un utilisateur connecté peut résoudre l'id.
 */
class UserPublicProfileAction
{
    private ServiceAuthnInterface $authnService;

    public function __construct(ServiceAuthnInterface $authnService)
    {
        $this->authnService = $authnService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id_user = $args['id_user'] ?? null;
        if (empty($id_user)) {
            throw new HttpNotFoundException($request, 'id_user requis.');
        }

        try {
            $user = $this->authnService->getUserById($id_user);
        } catch (\Exception $e) {
            throw new HttpNotFoundException($request, 'Utilisateur introuvable.');
        }

        $body = [
            'id' => $user->id,
            'prenom' => $user->prenom,
            'nom' => $user->nom,
        ];

        $response->getBody()->write(json_encode($body));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
