<?php
namespace application_core\application\usecases\interfaces;


interface ServiceAuthzUserInterface {

    /**
     * Orchestre la connexion.
     * @param InputAuthnDTO $user_dto Les identifiants (email/mdp)
     * @param string $host Le nom d'hôte (ex: "api.mondomaine.com")
     * @return string Le token JWT
     */
    public function signin(InputAuthnDTO $user_dto, string $host) : string; // Modifié ici

    public function signup(InputUserDTO $user_dto, ?string $role = 'client'): array;
}