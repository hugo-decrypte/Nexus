<?php

namespace application_core\application\usecases\interfaces;

use api\dtos\LogDTO;

interface ServiceLogInterface{
    public function getLogByActeur(string $id_user): array;
    public function getLogById(string $id): LogDTO;
    public function getLogs(): array;
}