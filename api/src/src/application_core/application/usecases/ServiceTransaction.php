<?php
namespace application_core\application\usecases;

use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;

class ServiceTransaction implements ServiceTransactionInterface {
    private TransactionRepositoryInterface $transactionRepository;

    public function calculSolde(string $idUtilisateur): float
    {
        // TODO: Implement calculSolde() method.
        return 0;
    }
}