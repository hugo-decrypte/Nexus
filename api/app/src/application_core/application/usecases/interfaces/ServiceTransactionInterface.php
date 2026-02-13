<?php

namespace application_core\application\usecases\interfaces;

use api\dtos\InputRechargementDTO;
use api\dtos\InputTransactionDTO;
use api\dtos\TransactionDTO;

interface ServiceTransactionInterface {
    public function getTransaction(string $id_user): array;
    public function getTransactions(): array;
    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array;
    public function calculSolde(string $id_user): float;
    public function creerTransaction(InputTransactionDTO $transaction_dto): TransactionDTO;
    public function rechargerCompte(InputRechargementDTO $rechargement_dto): TransactionDTO;
}