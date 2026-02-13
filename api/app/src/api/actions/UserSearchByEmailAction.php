<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\exceptions\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class UserSearchByEmailAction
{
    private ServiceAuthnInterface $authnService;

    public function __construct(ServiceAuthnInterface $authnService)
    {
        $this->authnService = $authnService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $email = isset($params['email']) ? trim((string) $params['email']) : '';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new HttpNotFoundException($request, 'Paramètre email requis et valide.');
        }

        try {
            $user = $this->authnService->getUserByEmail($email);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, 'Aucun utilisateur avec cet email.');
        }

        $body = [
            'id' => $user->id,
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'email' => $user->email,
        ];

        $response->getBody()->write(json_encode($body));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
