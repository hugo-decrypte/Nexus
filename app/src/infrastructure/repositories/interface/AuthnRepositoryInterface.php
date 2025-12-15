<?php

namespace nexus\infra\repositories\interface;

use nexus\api\dtos\CredentialsDTO;
use nexus\core\domain\entities\user\Utilisateur;

interface AuthnRepositoryInterface {
    public function getUser(string $email) : Utilisateur;
    public function saveUser(CredentialsDTO $credential, ?int $role = 1): void;
}