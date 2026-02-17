<?php

namespace infrastructure\repositories\interfaces;

use api\dtos\CredentialsDTO;
use api\dtos\CredentialsGoogleDTO;
use application_core\domain\entities\utilisateur\Utilisateur;

interface GoogleRepositoryInterface {
    public function saveGoogleUser(string $idUser, string $idGoogle): void;
}