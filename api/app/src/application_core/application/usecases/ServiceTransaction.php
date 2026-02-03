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
        return $this->transaction_repository->calculSolde($id_user);
    }

    public function getTransaction(string $id_user): TransactionDTO
    {
        try {
            $trans = $this->transaction_repository->getTransaction($id_user);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getMessage(), $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $this->toDTO($trans);
    }

    public function getTransactions(): array
    {
        try {
            $transactions = $this->transaction_repository->getTransactions();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return array_map(fn ($trans) => $this->toDTO($trans), $transactions);
    }

    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array
    {
        $transactions = $this->transaction_repository->getTransactionsBetween($id_emetteur, $id_recepteur);
        return array_map(fn ($trans) => $this->toDTO($trans), $transactions);
    }

    public function creerTransaction(string $emetteur_id, string $recepteur_id, float $montant): TransactionDTO
    {
        $trans = $this->transaction_repository->creerTransaction($emetteur_id, $recepteur_id, $montant);
        return $this->toDTO($trans);
    }

    private function toDTO($trans): TransactionDTO
    {
        return new TransactionDTO(
            id: $trans->id,
            montant: $trans->montant,
            hash: $trans->hash,
            emetteur_id: $trans->emetteur_id,
            recepteur_id: $trans->recepteur_id
        );
    }
}