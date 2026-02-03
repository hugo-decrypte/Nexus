<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class UserSoldeAction
{
    private ServiceTransactionInterface $serviceTransaction;

    public function __construct(ServiceTransactionInterface $serviceTransaction)
    {
        $this->serviceTransaction = $serviceTransaction;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id_user = $args['id_user'] ?? null;
        if (empty($id_user)) {
            throw new HttpBadRequestException($request, 'id_user requis.');
        }

        $solde = $this->serviceTransaction->calculSolde($id_user);

        $response->getBody()->write(json_encode(['solde' => $solde]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
