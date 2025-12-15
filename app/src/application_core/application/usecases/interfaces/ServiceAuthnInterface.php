<?php
namespace nexus\core\application\usecases\interfaces;

use nexus\api\dtos\InputAuthnDTO;
use nexus\api\dtos\InputUserDTO;

interface ServiceAuthnInterface {

    /**
     * Orchestre la connexion.
     * @param InputAuthnDTO $user_dto Les identifiants (email/mdp)
     * @param string $host Le nom d'hôte (ex: "api.mondomaine.com")
     * @return string Le token JWT
     */
    public function login(InputAuthnDTO $user_dto, string $host) : string; // Modifié ici

    public function register(InputUserDTO $user_dto, ?int $role = 1): array;
}