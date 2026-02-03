<?php

declare(strict_types=1);

namespace api\actions;

use DI\NotFoundException;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class UserByIdAction
{
    private AuthnRepositoryInterface $authnRepository;

    public function __construct(AuthnRepositoryInterface $authnRepository)
    {
        $this->authnRepository = $authnRepository;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id_user = $args['id_user'] ?? null;
        if (empty($id_user)) {
            throw new HttpNotFoundException($request, 'id_user requis.');
        }

        try {
            $user = $this->authnRepository->getUserById($id_user);
        } catch (NotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        $body = [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $response->getBody()->write(json_encode($body));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
