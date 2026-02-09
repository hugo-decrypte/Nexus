<?php

namespace application_core\application\usecases\interfaces;

use api\dtos\LogDTO;

interface ServiceLogInterface{
    public function getLogByActeur(string $id_user): array;
    public function getLogById(string $id): LogDTO;
    public function getLogs(): array;
    public function creationLogTransaction(string $acteur_id,string $id_transaction, int $montant): void;
    public function creationLogReceptionTransaction (string $acteur_id,string $id_transaction): void;
    public function creationLogConnection (string $acteur_id): void;
    public function creationLogInscription(string $acteur_id): void;
    public function creationLogModifPassword (string $acteur_id): void;
}