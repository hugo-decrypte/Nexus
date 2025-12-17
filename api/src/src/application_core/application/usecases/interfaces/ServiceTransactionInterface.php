<?php
namespace application_core\application\usecases\interfaces;

interface ServiceTransactionInterface {
    public function calculSolde(string $idUtilisateur): float;
}