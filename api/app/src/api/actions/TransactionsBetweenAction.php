<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class TransactionsBetweenAction
{
    private ServiceTransactionInterface $serviceTransaction;

    public function __construct(ServiceTransactionInterface $serviceTransaction)
    {
        $this->serviceTransaction = $serviceTransaction;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id_emetteur = $args['id_emetteur'] ?? null;
        $id_recepteur = $args['id_recepteur'] ?? null;
        if (empty($id_emetteur) || empty($id_recepteur)) {
            throw new HttpBadRequestException($request, 'id_emetteur et id_recepteur requis.');
        }

        $transactions = $this->serviceTransaction->getTransactionsBetween($id_emetteur, $id_recepteur);
        $response->getBody()->write(json_encode($transactions));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
