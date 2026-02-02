<?php

namespace infrastructure\repositories;

use application_core\domain\entities\transaction\Transaction;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use PDO;
use Slim\Exception\HttpInternalServerErrorException;

class PDOTransactionRepository implements TransactionRepositoryInterface {

    private PDO $transaction_pdo;

    public function __construct(PDO $transaction_pdo) {
        $this->transaction_pdo = $transaction_pdo;
    }

    public function calculSolde(): float
    {
        // TODO: Implement calculSolde() method.
        return 0;
    }

    public function getTransaction(string $id_user): Transaction
    {
        try {
            $query = $this->transaction_pdo->query("SELECT transactions.id, transactions.emetteur_id, transactions.recepteur_id, transactions.montant, transactions.hash
                                          FROM transactions
                                          WHERE transactions.emetteur_id = '$id_user'
                                          OR transactions.recepteur_id = '$id_user'");
        } catch (HttpInternalServerErrorException $e) {
            //500
            throw new \Exception("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable $e) {
            throw new \Exception("Erreur lors de la reception de la transaction.");
        }

        $array = $query->fetch(PDO::FETCH_ASSOC);
        if(!$array) {
            throw new EntityNotFoundException("transaction introuvable.", "transaction");
        }

        return new Transaction(
            id: $array['id'],
            montant: $array['montant'],
            hash: $array['hash'],
            emetteur_id: $array['emetteur_id'],
            recepteur_id: $array['recepteur_id']
        );
    }
}