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

            $res = $this->serviceTransaction->getTransactions();
            $response->getBody()->write(json_encode($res));
            return $response->withHeader("Content-Type", "application/json");

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des transaction." . $e->getMessage());
        } catch(\Throwable $e){
            throw new \Exception($e->getMessage());
        }
    }
}
