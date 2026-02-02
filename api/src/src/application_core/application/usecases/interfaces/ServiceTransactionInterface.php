<?php

namespace application_core\application\usecases\interfaces;

use api\dtos\TransactionDTO;

interface ServiceTransactionInterface {
    public function getTransaction(string $id_user) : TransactionDTO;
    public function calculSolde(string $id_user): float;
}