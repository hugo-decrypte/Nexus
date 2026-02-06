<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class CreateTransactionAction
{
    private ServiceTransactionInterface $serviceTransaction;

    public function __construct(ServiceTransactionInterface $serviceTransaction)
    {
        $this->serviceTransaction = $serviceTransaction;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $transaction_dto = $request->getAttribute('transaction_dto') ?? null;

        if(is_null($transaction_dto)) {
            throw new \Exception("Erreur récupération DTO de création d'un utilisateur");
        }

        try {
            $transaction = $this->serviceTransaction->creerTransaction($transaction_dto);
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, 'Erreur: ' . $e->getMessage());
        }

        $response->getBody()->write(json_encode($transaction));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}
