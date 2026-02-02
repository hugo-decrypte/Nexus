<?php

namespace api\actions;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TransactionAction {
    private ServiceTransactionInterface $serviceTransaction;

    public function __construct(ServiceTransactionInterface $serviceTransaction) {
        $this->serviceTransaction = $serviceTransaction;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id_user = $args['id_user'] ?? null;
        if(empty($id_user)) {
            throw new \Exception("Saisissez un id d'utilisateur'");
        }

        try {
            $transaction = $this->serviceTransaction->getTransaction($id_user);
            $response->getBody()->write(json_encode($transaction));

            return $response->withHeader("Content-Type", "application/json");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}