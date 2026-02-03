<?php

namespace application_core\application\usecases\interfaces;

use api\dtos\TransactionDTO;

interface ServiceTransactionInterface {
    public function getTransaction(string $id_user): TransactionDTO;
    public function getTransactions(): array;
    public function calculSolde(string $id_user): float;
    public function creerTransaction(string $emetteur_id, string $recepteur_id, float $montant): TransactionDTO;
}