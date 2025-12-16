<?php

namespace infrastructure\repositories;

use api\dtos\CredentialsDTO;
use application_core\domain\entities\utilisateur\Utilisateur;
use DI\NotFoundException;
use Exception;
use infrastructure\repositories\interface\AuthnRepositoryInterface;
use PDO;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpInternalServerErrorException;

class PDOAuthnRepository implements AuthnRepositoryInterface {


    private PDO $authn_pdo;

    public function __construct(PDO $authn_pdo) {
        $this->authn_pdo = $authn_pdo;
    }

    public function obtenirUtilisateur(string $email): Utilisateur
    {
        try {
            $query = $this->authn_pdo->query("SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateur WHERE email = '$email'");
            $res = $query->fetch(PDO::FETCH_ASSOC);
        } catch (HttpInternalServerErrorException) {
            //500
            throw new HttpInternalServerErrorException("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable) {
            throw new Exception("Erreur lors de la reception de l'utilisateur.");
        }
        if (!$res) {
            //404
            throw new NotFoundException("L'utilisateur ayant pour email ".$email." n'existe pas.");
        } else {
            return new Utilisateur(
                id: $res['id'],
                email: $res['email'],
                nom: $res['nom'],
                prenom: $res['prenom'],
                mot_de_passe: $res['mot_de_passe'],
                role: $res['role']
            );
        }
    }

    public function sauvegarderUtilisateur(CredentialsDTO $cred, ?string $role = 'client'): void
    {
        try {
            $id = Uuid::uuid4()->toString();
            // Le mot de passe est hashé dans le DTO
            $stmt = $this->authn_pdo->prepare(
                "INSERT INTO utilisateur (id, nom, prenom, email, mot_de_passe, role) VALUES (:id, :nom, :prenom, :email, :mdp, :role)"
            );
            $stmt->execute([
                'id' => $id,
                'nom' => $cred->nom,
                'prenom' => $cred->prenom,
                'email' => $cred->email,
                'mdp' => $cred->mot_de_passe,
                'role' => $role
            ]);

        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage());
        }
    }
}