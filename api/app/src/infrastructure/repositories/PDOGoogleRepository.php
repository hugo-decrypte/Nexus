<?php

namespace infrastructure\repositories;

use infrastructure\repositories\interfaces\GoogleRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpInternalServerErrorException;

class PDOGoogleRepository implements GoogleRepositoryInterface
{

    private PDO $google_pdo;

    public function __construct(PDO $google_pdo)
    {
        $this->google_pdo = $google_pdo;
    }

    public function saveGoogleUser(string $idUser, string $idGoogle): void
    {
        try {
            $id = Uuid::uuid4()->toString();

            $stmt = $this->pdo->prepare("
                INSERT INTO utilisateursGoogle (id, utilisateur_id, google_id) 
                VALUES (:id, :utilisateur_id, :google_id)
            ");

            $stmt->execute([
                'id' => $id,
                'utilisateur_id' => $idUser,
                'google_id' => $idGoogle
            ]);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage(), 400);
        }
    }
}