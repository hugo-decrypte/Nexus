<?php
namespace application_core\application\usecases;

use _PHPStan_b22655c3f\Nette\Neon\Exception;
use api\dtos\CredentialsDTO;
use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;
use api\dtos\UserDTO;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\exceptions\EntityNotFoundException;
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

    public function getUserById(string $user_id): UserDTO{
        try {
            return $this->toDTO($this->authnRepository->getUserById($user_id));
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." non trouvé", $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception("Probleme lors de la récupération de l'utilisateur.", $e->getCode());
        }
    }

    public function getUsers(): array{
        try {
            $users = $this->authnRepository->getUsers();
            return array_map(function($user) {
                return $this->toDTO($user);
            }, $users);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." non trouvé", $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception("Probleme lors de la récupération de la liste des utilisateurs.", $e->getCode());
        }
    }
    public function deleteUser($id_user): void{
        try {
            $this->authnRepository->deleteUser($id_user);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." non trouvé", $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception("Probleme lors de la récupération de la liste des utilisateurs.", $e->getCode());
        }
    }

    private function toDTO($user): UserDTO
    {
        return new UserDTO([
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
}