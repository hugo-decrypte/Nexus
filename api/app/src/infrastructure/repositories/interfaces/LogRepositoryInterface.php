<?php

namespace infrastructure\repositories\interfaces;

use application_core\domain\entities\log\Log;

interface LogRepositoryInterface{
    public function getLogByActeur(string $id_user): array;
    public function getLogById(string $id): Log;
    public function getLogs(): array;
}