<?php

namespace infrastructure\repositories\interfaces;

use application_core\domain\entities\log\Log;

interface LogRepositoryInterface{
    public function getLogByActeur(string $id_user): array;
    public function getLogById(string $id): Log;
    public function getLogs(): array;
    public function creationLogTransaction (string $acteur_id,string $id_transaction, int $montant): void;
    public function creationLogReceptionTransaction (string $acteur_id,string $id_transaction): void;
    public function creationLogConnection (string $acteur_id): void;
}
