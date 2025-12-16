<?php
namespace application_core\application\usecases;

use api\dtos\CredentialsDTO;
use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use Firebase\JWT\JWT;
use infrastructure\repositories\interface\AuthnRepositoryInterface;

class ServiceAuthn implements ServiceAuthnInterface {

    private AuthnProviderInterface $utilisateurProvider;
    private AuthnRepositoryInterface $authnRepository;
    private string $secretKey;

    public function __construct(AuthnProviderInterface $provider, AuthnRepositoryInterface $authnRepository, $jwtSecret) {
        $this->userProvider = $provider;
        $this->authnRepository = $authnRepository;
        $this->secretKey = $jwtSecret;
    }

    public function connecter(InputAuthnDTO $utilisateur_dto, string $host) : string {

        // 1. On valide l'utilisateur
        $utilisateur = $this->userProvider->connecter($utilisateur_dto);

        // 2. On construit le payload
        $payload = [
            "iss" => $host,
            "aud" => $host,
            "iat" => time(),
            "exp" => time() + 3600,
            "sub" => $utilisateur->id,
            "data" => [
                "email" => $utilisateur->email,
                "role" => $utilisateur->role,
            ]
        ];

        // 3. On encode et on return
        return JWT::encode($payload, $this->secretKey, 'HS512');
    }

    public function enregister(InputUserDTO $utilisateur_dto, ?string $role = "client"): array {
        try {
            $passwordhash = password_hash($utilisateur_dto->mot_de_passe, PASSWORD_BCRYPT);
            $credential = new CredentialsDTO($utilisateur_dto->email, $passwordhash);

            $this->authnRepository->sauvegarderUtilisateur($credential, $role);
        } catch (\Exception $e) {
            return [
                'status' => $e->getCode(),
                'success' => false,
                "message" => $e->getMessage()
            ];
        }
        return [
            'status' => 201,
            'success' => true,
            "message" => "Utilisateur ajouté avec succès."
        ];
    }
}