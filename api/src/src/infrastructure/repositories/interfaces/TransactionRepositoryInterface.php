<?php

namespace infrastructure\repositories\interfaces;

use application_core\domain\entities\transaction\Transaction;

interface TransactionRepositoryInterface {
    public function calculSolde(): float;
    public function getTransaction(string $id_user): Transaction;
    public function getTransactions(): array;
}