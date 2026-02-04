<?php

namespace infrastructure\repositories;

use api\dtos\CredentialsDTO;
use application_core\domain\entities\utilisateur\Utilisateur;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use PDO;
use Ramsey\Uuid\Uuid;

class PDOAuthnRepository implements AuthnRepositoryInterface {


    private PDO $authn_pdo;

    public function __construct(PDO $authn_pdo) {
        $this->authn_pdo = $authn_pdo;
    }

    public function getUserByEmail(string $email): Utilisateur
    {
        $stmt = $this->authn_pdo->prepare(
            "SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateurs WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            throw new EntityNotFoundException("L'utilisateur ayant pour email " . $email . " n'existe pas.", 'utilisateur');
        }
        return new Utilisateur(
            id: $res['id'],
            nom: $res['nom'],
            prenom: $res['prenom'],
            email: $res['email'],
            mot_de_passe: $res['mot_de_passe'],
            role: $res['role']
        );
    }

    public function getUserById(string $id): Utilisateur
    {
        $stmt = $this->authn_pdo->prepare(
            "SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateurs WHERE id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            throw new EntityNotFoundException("L'utilisateur ayant pour id " . $id . " n'existe pas.", 'utilisateur');
        }
        return new Utilisateur(
            id: $res['id'],
            nom: $res['nom'],
            prenom: $res['prenom'],
            email: $res['email'],
            mot_de_passe: $res['mot_de_passe'],
            role: $res['role']
        );
    }

    public function getUsers(): array
    {
        $stmt = $this->authn_pdo->query(
            "SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateurs ORDER BY nom, prenom"
        );
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new Utilisateur(
                id: $row['id'],
                nom: $row['nom'],
                prenom: $row['prenom'],
                email: $row['email'],
                mot_de_passe: $row['mot_de_passe'],
                role: $row['role']
            );
        }
        return $users;
    }

    public function deleteUser(string $id): void
    {
        $stmt = $this->authn_pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) {
            throw new EntityNotFoundException("L'utilisateur ayant pour id " . $id . " n'existe pas.", 'utilisateur');
        }
    }

    public function saveUser(CredentialsDTO $cred, ?string $role = 'client'): void
    {
        try {
            $id = Uuid::uuid4()->toString();
            // Le mot de passe est hashé dans le DTO
            $stmt = $this->authn_pdo->prepare(
                "INSERT INTO utilisateurs (id, nom, prenom, email, mot_de_passe, role) VALUES (:id, :nom, :prenom, :email, :mdp, :role)"
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