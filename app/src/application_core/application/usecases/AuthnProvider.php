<?php

namespace nexus\core\application\usecases;
use nexus\api\dtos\AuthnDTO;
use nexus\api\dtos\InputAuthnDTO;
use nexus\core\application\usecases\interfaces\AuthnProviderInterface;
use nexus\core\exceptions\ConnexionException;
use nexus\infra\repositories\interface\AuthnRepositoryInterface;

class AuthnProvider implements AuthnProviderInterface {

    private AuthnRepositoryInterface $authRepository;

    public function __construct(AuthnRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws ConnexionException
     */
    public function connecter(InputAuthnDTO $utilisateur_dto): AuthnDTO {

        $utilisateur = null;

        try {
            // On essaie de récupérer l'utilisateur
            $utilisateur = $this->authRepository->obtenirUtilisateur($utilisateur_dto->email);
        } catch (\Exception $e) {
            // Si le repo lève une exception (ex: NotFound), on l'ignore ici.
            // $utilisateur restera 'null', et c'est le 'if' suivant qui gérera l'erreur.
        }

        if (!$utilisateur || !password_verify($utilisateur_dto->mot_de_passe, $utilisateur->mot_de_passe)) {
            // On lève la MÊME erreur dans tous les cas d'échec
            throw new ConnexionException("Identifiants incorrects.");
        }

        return new AuthnDTO(
            id: $utilisateur->id,
            email: $utilisateur->email,
            nom: $utilisateur->nom,
            prenom: $utilisateur->prenom,
            role: $utilisateur->role
        );
    }
}