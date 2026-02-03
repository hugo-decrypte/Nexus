<?php

namespace infrastructure\repositories\interfaces;

use api\dtos\CredentialsDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface AuthnRepositoryInterface {
    public function obtenirUtilisateur(string $email): Utilisateur;
    public function obtenirUtilisateurParId(string $id): Utilisateur;
    public function obtenirTousLesUtilisateurs(): array;
    public function sauvegarderUtilisateur(CredentialsDTO $credential, ?string $role = "client"): void;
}