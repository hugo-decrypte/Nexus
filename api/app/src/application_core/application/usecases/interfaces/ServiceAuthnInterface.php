<?php
namespace application_core\application\usecases\interfaces;

use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;
use api\dtos\UserDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface ServiceAuthnInterface {

    public function signin(InputAuthnDTO $user_dto, string $host) : array;
    public function signup(InputUserDTO $user_dto, ?string $role = 'client'): array;
    public function getUserById(string $user_id): UserDTO;
    public function getUserByEmail(string $email): UserDTO;
    public function getUsers(): array;
    public function deleteUser($id_user): void;
    public function validateAccount(string $token): void;
}