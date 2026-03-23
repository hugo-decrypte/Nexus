<?php

namespace infrastructure\repositories\interfaces;

use api\dtos\CredentialsDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface AuthnRepositoryInterface {
    public function getUserByEmail(string $email): Utilisateur;
    public function getUserAdmin(): Utilisateur;
    public function getUserById(string $id): Utilisateur;
    public function getUsers(): array;
    public function deleteUser(string $id): void;
    public function saveUser(CredentialsDTO $cred, ?string $role = "client"): string;
    public function updateUser(string $id, string $nom, string $prenom, string $email): void;
    public function updatePassword(string $id, string $hashedPassword): void;
    public function validateAccount(string $token): void;

    public function setLoginOtp(string $userId, string $codeHash, string $expiresAt): void;

    /** @return array{hash: string, expires_at: string}|null */
    public function getLoginOtp(string $userId): ?array;

    public function clearLoginOtp(string $userId): void;
}