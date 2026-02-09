<?php
namespace application_core\application\usecases\interfaces;

use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;
use api\dtos\UserDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface ServiceAuthnInterface {

    /**
     * Orchestre la connexion.
     * @param InputAuthnDTO $user_dto Les identifiants (email/mdp)
     * @param string $host Le nom d'hôte (ex: "api.mondomaine.com")
     * @return string Le token JWT
     */
    public function signin(InputAuthnDTO $user_dto, string $host) : array;

    public function signup(InputUserDTO $user_dto, ?string $role = 'client'): array;
    public function getUserById(string $user_id): UserDTO;
    public function getUserByEmail(string $email): UserDTO;
    public function getUsers(): array;
    public function deleteUser($id_user): void;
}