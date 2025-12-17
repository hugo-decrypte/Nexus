<?php

namespace infrastructure\repositories;

use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use PDO;

class PDOTransactionRepository implements TransactionRepositoryInterface {

    private PDO $transaction_pdo;

    public function __construct(PDO $authn_pdo) {
        $this->authn_pdo = $authn_pdo;
    }

    public function calculSolde(): float
    {
        // TODO: Implement calculSolde() method.
        return 0;
    }
}