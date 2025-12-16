<?php

namespace infrastructure\repositories\interface;

use api\dtos\CredentialsDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface AuthnRepositoryInterface {
    public function obtenirUtilisateur(string $email) : Utilisateur;
    public function sauvegarderUtilisateur(CredentialsDTO $credential, ?string $role = "client"): void;
}