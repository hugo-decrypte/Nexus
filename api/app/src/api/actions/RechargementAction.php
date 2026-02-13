<?php

declare(strict_types=1);

namespace api\actions;

use api\dtos\InputRechargementDTO;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class RechargementAction
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

        $data = $request->getParsedBody();
        if (!is_array($data)) {
            throw new HttpBadRequestException($request, 'Body JSON invalide.');
        }

        $montant = isset($data['montant']) ? (float) $data['montant'] : null;
        if ($montant === null || $montant <= 0) {
            throw new HttpBadRequestException($request, 'montant (positif) requis.');
        }

        try {
            $transaction_dto = new InputRechargementDTO([
                'id_recepteur' => $id_user,
                'montant' => $montant
            ]);

            $transaction = $this->serviceTransaction->rechargerCompte($transaction_dto);
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
