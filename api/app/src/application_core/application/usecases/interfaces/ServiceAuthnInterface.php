<?php
namespace application_core\application\usecases\interfaces;

use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;

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