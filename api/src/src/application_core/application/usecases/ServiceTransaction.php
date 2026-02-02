<?php

namespace application_core\application\usecases;

use api\dtos\TransactionDTO;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;

class ServiceTransaction implements ServiceTransactionInterface {
    private TransactionRepositoryInterface $transaction_repository;

    public function __construct(TransactionRepositoryInterface $transaction_repository) {
        $this->transaction_repository = $transaction_repository;
    }

    public function calculSolde(string $id_user): float
    {
        // TODO: Implement calculSolde() method.
        return 0;
    }

    public function getTransaction(string $id_user) : TransactionDTO
    {
        try {
            $trans = $this->transaction_repository->getTransaction($id_user);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getMessage(), $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return new TransactionDTO(
            id: $trans->id,
            montant: $trans->montant,
            hash: $trans->hash,
            emetteur_id: $trans->emetteur_id,
            recepteur_id: $trans->recepteur_id
        );
    }
}