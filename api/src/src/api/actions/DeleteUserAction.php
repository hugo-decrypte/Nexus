<?php

declare(strict_types=1);

namespace api\actions;

use DI\NotFoundException;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class DeleteUserAction
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
            $this->authnRepository->supprimerUtilisateur($id_user);
        } catch (NotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        return $response->withStatus(204);
    }
}
