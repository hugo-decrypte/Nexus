<?php

namespace infrastructure\repositories;

use application_core\domain\entities\log\Log;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\LogRepositoryInterface;
use PDO;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpInternalServerErrorException;

class PDOLogRepository implements LogRepositoryInterface{

    private PDO $log_pdo;

    public function __construct(PDO $log_pdo) {
        $this->log_pdo = $log_pdo;
    }

    /**
     * @return Log[] Un tableau d'objets Log
     */
    public function getLogByActeur(string $id_user): array
    {
        try {
            $stmt = $this->log_pdo->prepare(
                "SELECT id, acteur_id, action_type, details, date_creation FROM logs 
             WHERE acteur_id = :id ORDER BY created_at DESC"
            );
            $stmt->execute(['id' => $id_user]);

            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$array) {
                throw new EntityNotFoundException("Aucun log trouvé pour cet utilisateur.", "log");
            }

            return array_map(function($row) {
                return new Log(
                    id: $row['id'],
                    action_type: $row['action_type'],
                    acteur_id: $row['acteur_id'],
                    details: is_string($row['details']) ? json_decode($row['details'], true) : $row['details'],
                    created_at: $row['date_creation']
                );
            }, $array);

        } catch (EntityNotFoundException $e) {
            throw $e; // On laisse remonter l'exception métier
        } catch (\PDOException $e) {
            throw new \Exception("Erreur SQL : " . $e->getMessage(), 500);
        } catch (\Throwable $t) {
            throw new \Exception("Erreur lors de la récupération des logs de l'utilisateur.", 400);
        }
    }

    public function getLogById(string $id): Log
    {
        try {
            $stmt = $this->log_pdo->prepare(
                "SELECT id, acteur_id, action_type, details, date_creation FROM logs
             WHERE id = :id  ORDER BY date_creation"
            );
            $stmt->execute(['id' => $id,]);
            $array = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$array) {
                throw new EntityNotFoundException("Log introuvable.", "log");
            }
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la récupération du log.", 400);
        }
        return new Log(
            id: $array['id'],
            acteur_id: $array['acteur_id'],
            action_type: $array['action_type'],
            details: is_string($array['details']) ? json_decode($array['details'], true) : $array['details'],
            created_at: $array['date_creation']
        );
    }

    public function getLogs(): array
    {
        try {
            $stmt = $this->log_pdo->prepare(
                "SELECT id, acteur_id, action_type, details, date_creation FROM logs"
            );
            $stmt->execute();

            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$array) {
                throw new EntityNotFoundException("Aucun log trouvé pour cet utilisateur.", "log");
            }

            return array_map(function($row) {
                return new Log(
                    id: $row['id'],
                    action_type: $row['action_type'],
                    acteur_id: $row['acteur_id'],
                    details: is_string($row['details']) ? json_decode($row['details'], true) : $row['details'],
                    created_at: $row['date_creation']
                );
            }, $array);

        } catch (EntityNotFoundException $e) {
            throw $e; // On laisse remonter l'exception métier
        } catch (\PDOException $e) {
            throw new \Exception("Erreur SQL : " . $e->getMessage(), 500);
        } catch (\Throwable $t) {
            throw new \Exception("Erreur lors de la récupération des logs.", 400);
        }
    }
    public function creationLogTransaction (string $acteur_id,string $id_transaction, int $montant): void{
        try {
            $id = Uuid::uuid4()->toString();
            $details = json_encode(array('transaction_id' => $id_transaction, 'montant' => $montant));

            $stmt = $this->log_pdo->prepare(
                "INSERT INTO logs (id, acteur_id, action_type, details) VALUES (:id, :acteur_id, :action_type, :details)"
            );
            $stmt->execute([
                'id' => $id,
                'acteur_id' => $acteur_id,
                'action_type' => "CREATION_TRANSACTION",
                'details' => $details,
            ]);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement du log de la transaction : " . $e->getMessage(), 400);
        }
    }
    public function creationLogReceptionTransaction (string $acteur_id,string $id_transaction): void{
        try {
            $id = Uuid::uuid4()->toString();
            $details = json_encode(array('transaction_id' => $id_transaction));

            $stmt = $this->log_pdo->prepare(
                "INSERT INTO logs (id, acteur_id, action_type, details) VALUES (:id, :acteur_id, :action_type, :details)"
            );
            $stmt->execute([
                'id' => $id,
                'acteur_id' => $acteur_id,
                'action_type' => "RECEPTION_PAIEMENT",
                'details' => $details,
            ]);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement du log de la reception de la transaction : " . $e->getMessage(), 400);
        }
    }
    public function creationLogConnection (string $acteur_id): void{
        try {
            $id = Uuid::uuid4()->toString();
            $details = json_encode(array('acteur_id' => $acteur_id));

            $stmt = $this->log_pdo->prepare(
                "INSERT INTO logs (id, acteur_id, action_type, details) VALUES (:id, :acteur_id, :action_type, :details)"
            );
            $stmt->execute([
                'id' => $id,
                'acteur_id' => $acteur_id,
                'action_type' => "CONNEXION",
                'details' => $details,
            ]);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement du log de la connexion d'un utilisateur : " . $e->getMessage(), 400);
        }
    }
    public function creationLogInscription(string $acteur_id): void{
        try {
            $id = Uuid::uuid4()->toString();
            $details = json_encode(array('acteur_id' => $acteur_id));

            $stmt = $this->log_pdo->prepare(
                "INSERT INTO logs (id, acteur_id, action_type, details) VALUES (:id, :acteur_id, :action_type, :details)"
            );
            $stmt->execute([
                'id' => $id,
                'acteur_id' => $acteur_id,
                'action_type' => "INSCRIPTION",
                'details' => $details,
            ]);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement du log de l'inscription d'un utilisateur : " . $e->getMessage(), 400);
        }
    }
    public function creationLogModifPassword (string $acteur_id): void{
        try {
            $id = Uuid::uuid4()->toString();
            $details = json_encode(array('acteur_id' => $acteur_id));

            $stmt = $this->log_pdo->prepare(
                "INSERT INTO logs (id, acteur_id, action_type, details) VALUES (:id, :acteur_id, :action_type, :details)"
            );
            $stmt->execute([
                'id' => $id,
                'acteur_id' => $acteur_id,
                'action_type' => "MODIF_MDP",
                'details' => $details,
            ]);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\PDOException $e) {
            throw new \Exception("Erreur lors de l'enregistrement du log de modification d'un mot de passe d'un utilisateur : " . $e->getMessage(), 400);
        }
    }
}