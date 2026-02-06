<?php

namespace infrastructure\repositories;

use application_core\domain\entities\log\Log;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\LogRepositoryInterface;
use PDO;
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
                "SELECT id, acteur_id, action_type, details, created_at FROM logs 
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
                    acteur_id: (int) $row['acteur_id'],
                    details: is_string($row['details']) ? json_decode($row['details'], true) : $row['details'],
                    created_at: $row['created_at']
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
                "SELECT id, acteur_id, action_type, details, created_at FROM logs
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
            acteur_id: (float) $array['acteur_id'],
            action_type: $array['action_type'],
            details: is_string($array['details']) ? json_decode($array['details'], true) : $array['details'],
            created_at: $array['created_at']
        );
    }

    public function getLogs(): array
    {
        try {
            $stmt = $this->log_pdo->prepare(
                "SELECT id, acteur_id, action_type, details, created_at FROM logs"
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
                    acteur_id: (int) $row['acteur_id'],
                    details: is_string($row['details']) ? json_decode($row['details'], true) : $row['details'],
                    created_at: $row['created_at']
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
}