<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class UpdatePasswordAction
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

        // Récupérer les données du corps de la requête
        $body = $request->getParsedBody();
        $currentPassword = $body['mot_de_passe_actuel'] ?? null;
        $newPassword = $body['nouveau_mot_de_passe'] ?? null;

        // Validation
        if (empty($currentPassword) || empty($newPassword)) {
            throw new HttpBadRequestException($request, 'Les champs mot_de_passe_actuel et nouveau_mot_de_passe sont requis.');
        }

        if (strlen($newPassword) < 6) {
            throw new HttpBadRequestException($request, 'Le nouveau mot de passe doit faire au moins 6 caractères.');
        }

        // Appeler le service pour mettre à jour le mot de passe
        $result = $this->authnService->updatePassword($id_user, $currentPassword, $newPassword);

        $response->getBody()->write(json_encode([
            'success' => $result['success'],
            'message' => $result['message']
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($result['status']);
    }
}
