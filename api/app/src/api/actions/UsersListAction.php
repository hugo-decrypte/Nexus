<?php

declare(strict_types=1);

namespace api\actions;

use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use PHPUnit\Framework\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UsersListAction
{
    private AuthnRepositoryInterface $authnRepository;

    public function __construct(AuthnRepositoryInterface $authnRepository)
    {
        $this->authnRepository = $authnRepository;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $users = $this->authnRepository->getUsers();
            $body = array_map(function ($user) {
                return [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'role' => $user->role,
                ];
            }, $users);

            $response->getBody()->write(json_encode($body));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
