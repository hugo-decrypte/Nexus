<?php

namespace application_core\application\usecases;

use api\dtos\AuthnDTO;
use api\dtos\InputAuthnDTO;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use application_core\exceptions\ConnexionException;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;

class AuthnProvider implements AuthnProviderInterface {

    private AuthnRepositoryInterface $authRepository;

    public function __construct(AuthnRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws ConnexionException
     */
    public function signin(InputAuthnDTO $user_dto): AuthnDTO {

        $user = null;

        try {
            $user = $this->authRepository->getUser($user_dto->email);
        } catch (\Exception $e) {
        }

        if (!$user || !password_verify($user_dto->password, $user->password)) {
            throw new ConnexionException("Identifiants incorrects.");
        }

        return new AuthnDTO(
            id: $user->id,
            email: $user->email,
            role: $user->role
        );
    }
}