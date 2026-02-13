<?php

namespace infrastructure\repositories\interfaces;

use application_core\domain\entities\transaction\Transaction;

interface TransactionRepositoryInterface {
    public function calculSolde(string $id_user): float;
    public function getTransaction(string $id_user): array;
    public function getTransactions(): array;
    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array;
    public function getLastTransactionHash(): ?string;
    public function creerTransaction(string $emetteur_id, string $recepteur_id, float $montant, ?string $desc): Transaction;
    public function rechargerCompte(string $recepteur_id, float $montant): Transaction;
}