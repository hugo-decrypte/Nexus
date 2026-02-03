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
        $body = $request->getParsedBody();
        if (!is_array($body)) {
            throw new HttpBadRequestException($request, 'Body JSON invalide.');
        }

        $emetteur_id = $body['emetteur_id'] ?? $body['id_emetteur'] ?? null;
        $recepteur_id = $body['recepteur_id'] ?? $body['id_recepteur'] ?? null;
        $montant = isset($body['montant']) ? (float) $body['montant'] : null;

        if (empty($emetteur_id) || empty($recepteur_id) || $montant === null || $montant <= 0) {
            throw new HttpBadRequestException($request, 'emetteur_id, recepteur_id et montant (positif) requis.');
        }

        try {
            $transaction = $this->serviceTransaction->creerTransaction($emetteur_id, $recepteur_id, $montant);
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
