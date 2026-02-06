<?php

declare(strict_types=1);

namespace api\actions;

use _PHPStan_b22655c3f\Nette\Neon\Exception;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use DI\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class UserByIdAction
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
        } catch (HttpNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
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
