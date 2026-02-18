<?php

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TransactionsAction {
    private ServiceTransactionInterface $serviceTransaction;

    public function __construct(ServiceTransactionInterface $serviceTransaction){
        $this->serviceTransaction = $serviceTransaction;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $transactions = $this->serviceTransaction->getTransactions();
            $body = array_map(function ($t) {
                return [
                    'id' => $t->id,
                    'emetteur_id' => $t->emetteur_id,
                    'recepteur_id' => $t->recepteur_id,
                    'montant' => $t->montant,
                    'date_creation' => $t->created_at,
                    'description' => $t->description,
                ];
            }, $transactions);
            $response->getBody()->write(json_encode($body));
            return $response->withHeader("Content-Type", "application/json");
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des transaction." . $e->getMessage());
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
