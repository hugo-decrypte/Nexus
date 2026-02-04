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
        $stmt = $this->transaction_pdo->prepare(
            "SELECT COALESCE(SUM(CASE WHEN recepteur_id = :id THEN montant ELSE -montant END), 0) AS solde
             FROM transactions WHERE emetteur_id = :id2 OR recepteur_id = :id3"
        );
        $stmt->execute(['id' => $id_user, 'id2' => $id_user, 'id3' => $id_user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($row['solde'] ?? 0);
    }

    public function getTransaction(string $id_user): Transaction
    {
        $stmt = $this->transaction_pdo->prepare(
            "SELECT id, emetteur_id, recepteur_id, montant, hash FROM transactions
             WHERE emetteur_id = :id OR recepteur_id = :id2 ORDER BY date_creation DESC LIMIT 1"
        );
        $stmt->execute(['id' => $id_user, 'id2' => $id_user]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$array) {
            throw new EntityNotFoundException("transaction introuvable.", "transaction");
        }
        return new Transaction(
            id: $array['id'],
            montant: (float) $array['montant'],
            hash: $array['hash'],
            emetteur_id: $array['emetteur_id'],
            recepteur_id: $array['recepteur_id']
        );
    }

    public function getTransactions(): array
    {
        $stmt = $this->transaction_pdo->query(
            "SELECT id, emetteur_id, recepteur_id, montant, hash FROM transactions ORDER BY date_creation DESC"
        );
        $transactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $transactions[] = new Transaction(
                id: $row['id'],
                montant: (float) $row['montant'],
                hash: $row['hash'],
                emetteur_id: $row['emetteur_id'],
                recepteur_id: $row['recepteur_id']
            );
        }
        return $transactions;
    }

    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array
    {
        $stmt = $this->transaction_pdo->prepare(
            "SELECT id, emetteur_id, recepteur_id, montant, hash FROM transactions
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
                montant: (float) $row['montant'],
                hash: $row['hash'],
                emetteur_id: $row['emetteur_id'],
                recepteur_id: $row['recepteur_id']
            );
        }
        return $transactions;
    }

    public function getLastTransactionHash(): ?string
    {
        $stmt = $this->transaction_pdo->query(
            "SELECT hash FROM transactions ORDER BY date_creation DESC LIMIT 1"
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['hash'] : null;
    }

    public function creerTransaction(string $emetteur_id, string $recepteur_id, float $montant): Transaction
    {
        $prevHash = $this->getLastTransactionHash() ?? '';
        $newHash = hash('sha256', $prevHash . '|' . $emetteur_id . '|' . $recepteur_id . '|' . $montant);
        $id = Uuid::uuid4()->toString();

        $solde_emetteur = $this->calculSolde($emetteur_id);
        if ($solde_emetteur < $montant) {
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
            recepteur_id: $recepteur_id
        );
    }
}