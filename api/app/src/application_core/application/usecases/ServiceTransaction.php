<?php

namespace application_core\application\usecases;

use api\dtos\InputTransactionDTO;
use api\dtos\TransactionDTO;
use api\middlewares\CreateTransactionMiddleware;
use application_core\application\usecases\interfaces\ServiceLogInterface;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;

class ServiceTransaction implements ServiceTransactionInterface {
    private TransactionRepositoryInterface $transaction_repository;
    private ServiceLogInterface $serviceLog;

    public function __construct(TransactionRepositoryInterface $transaction_repository, ServiceLogInterface $serviceLog) {
        $this->transaction_repository = $transaction_repository;
        $this->serviceLog = $serviceLog;
    }

    public function calculSolde(string $id_user): float
    {
        try {
            return $this->transaction_repository->calculSolde($id_user);
        }catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getTransaction(string $id_user): array
    {
        try {
            $transactions = $this->transaction_repository->getTransaction($id_user);
            return array_map(function($transaction) {
                return $this->toDTO($transaction);
            }, $transactions);

        } catch (EntityNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \Exception("Erreur lors du traitement des transactions : " . $e->getMessage(), 500);
        }
    }

    public function getTransactions(): array
    {
        try {
            $transactions = $this->transaction_repository->getTransactions();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return array_map(fn ($trans) => $this->toDTO($trans), $transactions);
    }

    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array
    {
        try {
            $transactions = $this->transaction_repository->getTransactionsBetween($id_emetteur, $id_recepteur);
        } catch (\Exception $e) {
            throw new \Exception("probleme lors de la récupération des transactions.", $e->getCode());
        }
        return array_map(fn ($trans) => $this->toDTO($trans), $transactions);
    }

    public function creerTransaction(InputTransactionDTO $transaction_dto): TransactionDTO
    {
        try {
            $trans = $this->transaction_repository->creerTransaction($transaction_dto->id_emetteur, $transaction_dto->id_recepteur, $transaction_dto->montant, $transaction_dto->description);
            $this->serviceLog->creationLogTransaction($transaction_dto->id_emetteur, $trans->id, $transaction_dto->montant);
            $this->serviceLog->creationLogReceptionTransaction($transaction_dto->id_recepteur, $trans->id);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." non trouvé", $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->toDTO($trans);
    }

    private function toDTO($trans): TransactionDTO
    {
        return new TransactionDTO(
            id: $trans->id,
            montant: $trans->montant,
            hash: $trans->hash,
            emetteur_id: $trans->emetteur_id,
            recepteur_id: $trans->recepteur_id,
            description: $trans->description
        );
    }
}