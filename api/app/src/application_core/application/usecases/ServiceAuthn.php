<?php
namespace application_core\application\usecases;

use api\dtos\CredentialsDTO;
use api\dtos\InputAuthnDTO;
use api\dtos\InputUserDTO;
use api\dtos\UserDTO;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\application\usecases\interfaces\ServiceLogInterface;
use application_core\exceptions\AccountNotValidatedException;
use application_core\exceptions\ConnexionException;
use application_core\exceptions\EntityNotFoundException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use infrastructure\repositories\interfaces\MailSenderInterface;
use Slim\Exception\HttpInternalServerErrorException;

class ServiceAuthn implements ServiceAuthnInterface {

    private AuthnProviderInterface $userProvider;
    private AuthnRepositoryInterface $authnRepository;
    private ServiceLogInterface $serviceLog;
    private MailSenderInterface $mailSender;
    private string $secretKey;
    private bool $skipEmailOtp;

    public function __construct(
        AuthnProviderInterface $provider,
        AuthnRepositoryInterface $authnRepository,
        ServiceLogInterface $serviceLog,
        MailSenderInterface $mailSender,
        $jwtSecret,
        bool $skipEmailOtp = false
    ) {
        $this->userProvider = $provider;
        $this->authnRepository = $authnRepository;
        $this->serviceLog = $serviceLog;
        $this->secretKey = $jwtSecret;
        $this->mailSender = $mailSender;
        $this->skipEmailOtp = $skipEmailOtp;
    }

    public function signin(InputAuthnDTO $user_dto, string $host): array
    {
        try {
            $user = $this->userProvider->signin($user_dto);
        } catch (ConnexionException | AccountNotValidatedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }

        if ($this->skipEmailOtp) {
            $this->serviceLog->creationLogConnection($user->id);
            $userName = $this->getUserByEmail($user->email);

            return $this->buildSessionResponse($user->id, $user->email, $user->role, $userName->nom, $userName->prenom, $host);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = password_hash($code, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', time() + 600);
        $this->authnRepository->setLoginOtp($user->id, $hash, $expiresAt);

        $html = <<<HTML
        <html><body style="font-family:sans-serif;">
        <p>Votre code de connexion Nexus :</p>
        <p style="font-size:28px;font-weight:bold;letter-spacing:6px;">{$code}</p>
        <p>Valable 10 minutes. Si ce n'est pas vous, ignorez ce message.</p>
        </body></html>
        HTML;

        try {
            $this->mailSender->send($user->email, 'Code de connexion Nexus', $html);
        } catch (\Throwable $e) {
            $this->authnRepository->clearLoginOtp($user->id);
            throw new \Exception('Impossible d\'envoyer le code par e-mail. Réessayez plus tard.', 503);
        }

        $pendingPayload = [
            'iss' => $host,
            'aud' => $host,
            'iat' => time(),
            'exp' => time() + 600,
            'sub' => $user->id,
            'typ' => 'mfa_pending',
            'mfa_pending' => true,
        ];
        $pendingToken = JWT::encode($pendingPayload, $this->secretKey, 'HS512');

        return [
            'pending' => true,
            'pending_token' => $pendingToken,
            'email_masked' => self::maskEmail($user->email),
        ];
    }

    public function completeLoginWithOtp(string $pendingToken, string $code, string $host): array
    {
        $digits = preg_replace('/\D/', '', $code) ?? '';
        if (strlen($digits) !== 6) {
            throw new ConnexionException('Code à 6 chiffres requis.');
        }

        try {
            $decoded = JWT::decode(trim($pendingToken), new Key($this->secretKey, 'HS512'));
        } catch (\Exception $e) {
            throw new ConnexionException('Session expirée. Reconnectez-vous.');
        }

        if (empty($decoded->mfa_pending) || ($decoded->typ ?? '') !== 'mfa_pending' || empty($decoded->sub)) {
            throw new ConnexionException('Token invalide.');
        }

        $userId = (string) $decoded->sub;
        $otp = $this->authnRepository->getLoginOtp($userId);
        if ($otp === null) {
            throw new ConnexionException('Code expiré ou déjà utilisé. Reconnectez-vous.');
        }

        if (strtotime($otp['expires_at']) < time()) {
            $this->authnRepository->clearLoginOtp($userId);
            throw new ConnexionException('Code expiré. Reconnectez-vous.');
        }

        if (!password_verify($digits, $otp['hash'])) {
            throw new ConnexionException('Code incorrect.');
        }

        $this->authnRepository->clearLoginOtp($userId);
        $entity = $this->authnRepository->getUserById($userId);
        $this->serviceLog->creationLogConnection($userId);
        $userName = $this->getUserByEmail($entity->email);

        $session = $this->buildSessionResponse(
            $entity->id,
            $entity->email,
            $entity->role,
            $userName->nom,
            $userName->prenom,
            $host
        );

        return [
            'id' => $session['id'],
            'nom' => $session['nom'],
            'prenom' => $session['prenom'],
            'role' => $session['role'],
            'email' => $entity->email,
            'token' => $session['token'],
        ];
    }

    /**
     * @return array{id: string, nom: string, prenom: string, role: string, token: string}
     */
    private function buildSessionResponse(
        string $userId,
        string $email,
        string $role,
        string $nom,
        string $prenom,
        string $host
    ): array {
        $payload = [
            'iss' => $host,
            'aud' => $host,
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $userId,
            'data' => [
                'email' => $email,
                'role' => $role,
            ],
        ];

        return [
            'id' => $userId,
            'nom' => $nom,
            'prenom' => $prenom,
            'role' => $role,
            'token' => JWT::encode($payload, $this->secretKey, 'HS512'),
        ];
    }

    private static function maskEmail(string $email): string
    {
        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) {
            return '***';
        }
        [$local, $domain] = $parts;
        $len = strlen($local);
        $visible = $len <= 2 ? str_repeat('*', max(1, $len)) : substr($local, 0, 2) . '***';

        return $visible . '@' . $domain;
    }

    public function signup(InputUserDTO $user_dto, ?string $role = "client"): array {
        try {
            $passwordhash = password_hash($user_dto->mot_de_passe, PASSWORD_BCRYPT);
            $credential = new CredentialsDTO($user_dto->nom, $user_dto->prenom, $user_dto->email, $passwordhash);
            $validation_token = $this->authnRepository->saveUser($credential, $role);
            $this->mailSender->send($user_dto->email, "Activation de votre compte Nexus - Action requise", <<<EOT
            <html>
            <body style="background-color: #f4f4f4;">
            <h2 style="color: #2c3e50;">Bienvenue sur Nexus</h2>
            <p>Cliquez ici :</p>
            <a href="http://localhost:6080/verify-email?token=$validation_token" style="color: blue;">
            Vérifier mon email
            </a>
            </body>
            </html>
            EOT
            );
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \PDOException($e->getMessage(), 400);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
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
    public function getUserByEmail(string $email): UserDTO{
        try {
            return $this->toDTO($this->authnRepository->getUserByEmail($email));
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
            $this->serviceLog->creationLogModifPassword($id_user);

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

    public function validateAccount(string $token): void
    {
        try {
            $this->authnRepository->validateAccount($token);
        } catch (EntityNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}