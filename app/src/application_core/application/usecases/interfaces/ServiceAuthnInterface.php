<?php
namespace nexus\core\application\usecases\interfaces;

use nexus\api\dtos\InputAuthnDTO;
use nexus\api\dtos\InputUserDTO;

interface ServiceAuthnInterface {

    /**
     * Orchestre la connexion.
     * @param InputAuthnDTO $utilisateur_dto Les identifiants (email/mdp)
     * @param string $host Le nom d'hôte (ex: "api.mondomaine.com")
     * @return string Le token JWT
     */
    public function connecter(InputAuthnDTO $utilisateur_dto, string $host) : string; // Modifié ici

    public function enregister(InputUserDTO $utilisateur_dto, ?string $role = 'client'): array;
}