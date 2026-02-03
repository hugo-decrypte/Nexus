<?php

namespace application_core\application\usecases\interfaces;

use api\dtos\TransactionDTO;

interface ServiceTransactionInterface {
    public function getTransaction(string $id_user): TransactionDTO;
    public function getTransactions(): array;
    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array;
    public function calculSolde(string $id_user): float;
    public function creerTransaction(string $emetteur_id, string $recepteur_id, float $montant): TransactionDTO;
}