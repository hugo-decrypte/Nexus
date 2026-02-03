<?php

namespace infrastructure\repositories\interfaces;

use api\dtos\CredentialsDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface AuthnRepositoryInterface {
    public function getUserByEmail(string $email): Utilisateur;
    public function getUserById(string $id): Utilisateur;
    public function getUsers(): array;
    public function deleteUser(string $id): void;
    public function saveUser(CredentialsDTO $cred, ?string $role = "client"): void;
}