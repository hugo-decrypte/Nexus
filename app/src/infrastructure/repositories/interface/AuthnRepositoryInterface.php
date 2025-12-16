<?php

namespace nexus\infra\repositories\interface;

use nexus\api\dtos\CredentialsDTO;
use nexus\core\domain\entities\utilisateur\Utilisateur;

interface AuthnRepositoryInterface {
    public function obtenirUtilisateur(string $email) : Utilisateur;
    public function sauvegarderUtilisateur(CredentialsDTO $credential, ?string $role = "client"): void;
}