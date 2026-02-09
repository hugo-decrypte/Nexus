<?php

namespace infrastructure\repositories;

use application_core\domain\entities\transaction\Transaction;
use application_core\exceptions\EntityNotFoundException;
use application_core\exceptions\NotEnoughMoneyException;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use PDO;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpInternalServerErrorException;

class PDOTransactionRepository implements TransactionRepositoryInterface {

    private PDO $transaction_pdo;
    private PDOAuthnRepository $authn_repository;

    public function __construct(PDO $transaction_pdo, PDOAuthnRepository $authn_repository) {
        $this->transaction_pdo = $transaction_pdo;
        $this->authn_repository = $authn_repository;
    }

    public function calculSolde(string $id_user): float
    {
        try {
            $stmt = $this->transaction_pdo->prepare(
                "SELECT COALESCE(SUM(CASE WHEN recepteur_id = :id THEN montant ELSE -montant END), 0) AS solde
             FROM transactions WHERE emetteur_id = :id2 OR recepteur_id = :id3"
            );
            $stmt->execute(['id' => $id_user, 'id2' => $id_user, 'id3' => $id_user]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la récupération du hash de la dernière transaction.", 400);
        }
        return (float) ($row['solde'] ?? 0);
    }
    public function getTransaction(string $id_user): array
    {
        try {
            $stmt = $this->transaction_pdo->prepare(
                "SELECT id, emetteur_id, recepteur_id, montant, hash, date_creation FROM transactions WHERE emetteur_id = :id OR recepteur_id = :id2 ORDER BY date_creation DESC"
            );

            $stmt->execute(['id' => $id_user, 'id2' => $id_user]);
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$array) {
                throw new EntityNotFoundException("Aucune transaction trouvée pour cet utilisateur.", "transaction");
            }
            return array_map(function(array $row) {
                return new Transaction(
                    id: $row['id'],
                    montant: (float) $row['montant'],
                    hash: $row['hash'],
                    emetteur_id: $row['emetteur_id'],
                    recepteur_id: $row['recepteur_id'],
                    created_at: $row['date_creation']
                );
            }, $array);

        } catch (EntityNotFoundException $e) {
            throw $e;
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'exécution de la requête SQL.", 500);
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la récupération des transactions de l'utilisateur.", 400);
        }
    }

    public function getTransactions(): array
    {
        try {
            $stmt = $this->transaction_pdo->query(
                "SELECT id, emetteur_id, recepteur_id, montant, hash, date_creation FROM transactions ORDER BY date_creation DESC"
            );
            $transactions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $transactions[] = new Transaction(
                    id: $row['id'],
                    montant: (float)$row['montant'],
                    hash: $row['hash'],
                    emetteur_id: $row['emetteur_id'],
                    recepteur_id: $row['recepteur_id'],
                    created_at: $row['date_creation']
                );
            }
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la récupération des transactions.", 400);
        }
        return $transactions;
    }

    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array
    {
        try {
            $stmt = $this->transaction_pdo->prepare(
                "SELECT id, emetteur_id, recepteur_id, montant, hash, date_creation FROM transactions
             WHERE (emetteur_id = :e1 AND recepteur_id = :r1) OR (emetteur_id = :e2 AND recepteur_id = :r2)
             ORDER BY date_creation DESC"
            );
            $stmt->execute([
                'e1' => $id_emetteur, 'r1' => $id_recepteur,
                'e2' => $id_recepteur, 'r2' => $id_emetteur,
            ]);
            $transactions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $transactions[] = new Transaction(
                    id: $row['id'],
                    montant: (float)$row['montant'],
                    hash: $row['hash'],
                    emetteur_id: $row['emetteur_id'],
                    recepteur_id: $row['recepteur_id'],
                    created_at: $row['date_creation']
                );
            }
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la récupération des transactions entre les utilisateurs.", 400);
        }
        return $transactions;
    }

    public function getLastTransactionHash(): ?string
    {
        try {
            $stmt = $this->transaction_pdo->query(
                "SELECT hash FROM transactions ORDER BY date_creation DESC LIMIT 1"
            );
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la récupération du hash de la dernière transaction.", 400);
        }
        return $row ? $row['hash'] : null;
    }

    public function creerTransaction(string $emetteur_id, string $recepteur_id, float $montant): Transaction
    {
        $prevHash = $this->getLastTransactionHash() ?? '';
        $newHash = hash('sha256', $prevHash . '|' . $emetteur_id . '|' . $recepteur_id . '|' . $montant);
        $id = Uuid::uuid4()->toString();

        $solde_emetteur = $this->calculSolde($emetteur_id);
        try {
            $role_emetteur = $this->authn_repository->getUserById($emetteur_id)->role;
        }catch(\Exception $e){
            throw new $e;
        }
        if ($solde_emetteur < $montant && $role_emetteur != "admin") {
            throw new NotEnoughMoneyException("L'emetteur n'a pas assez d'argent.");
        }

        try {
            $this->authn_repository->getUserById($emetteur_id);
            $this->authn_repository->getUserById($recepteur_id);
            $stmt = $this->transaction_pdo->prepare(
                "INSERT INTO transactions (id, emetteur_id, recepteur_id, montant, hash) VALUES (:id, :emetteur_id, :recepteur_id, :montant, :hash)"
            );
            $stmt->execute([
                'id' => $id,
                'emetteur_id' => $emetteur_id,
                'recepteur_id' => $recepteur_id,
                'montant' => $montant,
                'hash' => $newHash,
            ]);
        } catch (HttpInternalServerErrorException) {
            throw new \Exception("Erreur lors de l'execution de la requete SQL.", 500);
        } catch(EntityNotFoundException $e) {
            throw new EntityNotFoundException($e, 'utilisateur');
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de l'ajout de la transaction'.", 400);
        }

        return new Transaction(
            id: $id,
            montant: $montant,
            hash: $newHash,
            emetteur_id: $emetteur_id,
            recepteur_id: $recepteur_id,
            created_at: date('Y-m-d H:i:s')
        );
    }
}