<?php
namespace application_core\application\usecases;

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

    public function signin(InputAuthnDTO $user_dto, string $host) : array {

        // 1. On valide l'user
        try {
            $user = $this->userProvider->signin($user_dto);
        } catch(\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode());
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
                "role" => $user->role
            ]
        ];

        // 3. On encode et on return
        return [$user->id,JWT::encode($payload, $this->secretKey, 'HS512')];
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

    public function updateUser(string $id_user, string $nom, string $prenom, string $email): array {
        try {
            $this->authnRepository->updateUser($id_user, $nom, $prenom, $email);
            return [
                'status' => 200,
                'success' => true,
                "message" => "Utilisateur mis à jour avec succès."
            ];
        } catch (EntityNotFoundException $e) {
            return [
                'status' => 404,
                'success' => false,
                "message" => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => $e->getCode() ?: 500,
                'success' => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function updatePassword(string $id_user, string $currentPassword, string $newPassword): array {
        try {
            // Récupérer l'utilisateur pour vérifier le mot de passe actuel
            $user = $this->authnRepository->getUserById($id_user);

            // Vérifier que le mot de passe actuel est correct
            if (!password_verify($currentPassword, $user->mot_de_passe)) {
                return [
                    'status' => 401,
                    'success' => false,
                    "message" => "Le mot de passe actuel est incorrect."
                ];
            }

            // Hasher le nouveau mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe
            $this->authnRepository->updatePassword($id_user, $hashedPassword);

            return [
                'status' => 200,
                'success' => true,
                "message" => "Mot de passe mis à jour avec succès."
            ];
        } catch (EntityNotFoundException $e) {
            return [
                'status' => 404,
                'success' => false,
                "message" => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => $e->getCode() ?: 500,
                'success' => false,
                "message" => $e->getMessage()
            ];
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