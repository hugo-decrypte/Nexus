<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class UpdateUserAction
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
        $nom = $body['nom'] ?? null;
        $prenom = $body['prenom'] ?? null;
        $email = $body['email'] ?? null;

        // Validation
        if (empty($nom) || empty($prenom) || empty($email)) {
            throw new HttpBadRequestException($request, 'Les champs nom, prenom et email sont requis.');
        }

        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email invalide");
        }

        // Appeler le service pour mettre à jour l'utilisateur
        $result = $this->authnService->updateUser($id_user, $nom, $prenom, $email);

        $response->getBody()->write(json_encode([
            'success' => $result['success'],
            'message' => $result['message']
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($result['status']);
    }
}
