<?php
namespace application_core\application\usecases;

use _PHPStan_b22655c3f\Nette\Neon\Exception;
use api\dtos\CredentialsDTO;
use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use Firebase\JWT\JWT;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;

class ServiceAuthn implements ServiceAuthnInterface {

    private AuthnProviderInterface $userProvider;
    private AuthnRepositoryInterface $authnRepository;
    private string $secretKey;

    public function __construct(AuthnProviderInterface $provider, AuthnRepositoryInterface $authnRepository, $jwtSecret) {
        $this->userProvider = $provider;
        $this->authnRepository = $authnRepository;
        $this->secretKey = $jwtSecret;
    }

    public function signin(InputAuthnDTO $user_dto, string $host) : string {

        // 1. On valide l'user
        try {
            $user = $this->userProvider->signin($user_dto);
        } catch(\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }

        // 2. On construit le payload
        $payload = [
            "iss" => $host,
            "aud" => $host,
            "iat" => time(),
            "exp" => time() + 3600,
            "sub" => $user->id,
            "data" => [
                "email" => $user->email,
                "role" => $user->role,
            ]
        ];

        // 3. On encode et on return
        return JWT::encode($payload, $this->secretKey, 'HS512');
    }

    public function signup(InputUserDTO $user_dto, ?string $role = "client"): array {
        try {
            $passwordhash = password_hash($user_dto->mot_de_passe, PASSWORD_BCRYPT);
            $credential = new CredentialsDTO($user_dto->nom, $user_dto->prenom, $user_dto->email, $passwordhash);

            $this->authnRepository->saveUser($credential, $role);
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
            "message" => "user ajouté avec succès."
        ];
    }
}