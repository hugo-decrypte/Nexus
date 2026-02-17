<?php

namespace application_core\application\usecases;

use api\dtos\InputTransactionDTO;
use api\dtos\TransactionDTO;
use api\dtos\TransactionWithNameDTO;
use api\middlewares\CreateTransactionMiddleware;
use application_core\application\usecases\interfaces\ServiceLogInterface;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use application_core\exceptions\EntityNotFoundException;
use application_core\exceptions\SendMailException;
use infrastructure\repositories\interfaces\MailSenderInterface;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;

class ServiceTransaction implements ServiceTransactionInterface {
    private TransactionRepositoryInterface $transaction_repository;
    private AuthnRepositoryInterface $authn_repository;
    private ServiceLogInterface $serviceLog;
    private MailSenderInterface $mail_sender;

    public function __construct(TransactionRepositoryInterface $transaction_repository, AuthnRepositoryInterface $authn_repository, ServiceLogInterface $serviceLog, MailSenderInterface $mail_sender) {
        $this->transaction_repository = $transaction_repository;
        $this->authn_repository = $authn_repository;
        $this->serviceLog = $serviceLog;
        $this->mail_sender = $mail_sender;
    }

    public function calculSolde(string $id_user): float
    {
        try {
            return $this->transaction_repository->calculSolde($id_user);
        }catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getTransaction(string $id_user): array
    {
        try {
            $transactions = $this->transaction_repository->getTransaction($id_user);
            return array_map(function($transaction) {
                $user_emetteur = $this->authn_repository->getUserById($transaction->emetteur_id);
                $user_recepteur = $this->authn_repository->getUserById($transaction->recepteur_id);
                return $this->toDTOAvecNom($transaction,$user_emetteur,$user_recepteur);
            }, $transactions);

        } catch (EntityNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \Exception("Erreur lors du traitement des transactions : " . $e->getMessage(), 500);
        }
    }

    public function getTransactions(): array
    {
        try {
            $transactions = $this->transaction_repository->getTransactions();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return array_map(fn ($trans) => $this->toDTO($trans), $transactions);
    }

    public function getTransactionsBetween(string $id_emetteur, string $id_recepteur): array
    {
        try {
            $transactions = $this->transaction_repository->getTransactionsBetween($id_emetteur, $id_recepteur);
        } catch (\Exception $e) {
            throw new \Exception("probleme lors de la récupération des transactions.", $e->getCode());
        }
        return array_map(fn ($trans) => $this->toDTO($trans), $transactions);
    }

    public function creerTransaction(InputTransactionDTO $transaction_dto): TransactionDTO
    {
        try {
            $trans = $this->transaction_repository->creerTransaction($transaction_dto->id_emetteur, $transaction_dto->id_recepteur, $transaction_dto->montant, $transaction_dto->description);
            $this->serviceLog->creationLogTransaction($transaction_dto->id_emetteur, $trans->id, $transaction_dto->montant);
            $this->serviceLog->creationLogReceptionTransaction($transaction_dto->id_recepteur, $trans->id);
            $this->mail_sender->send(
                $trans->emetteur_email,
                'Confirmation de transaction',
                'Votre virement de ' . $transaction_dto->montant . 'PO a bien été effectué envers ' . $trans->recepteur_email . '.'
            );
            $this->mail_sender->send(
                $trans->recepteur_email,
                'Reception de transaction',
                'Vous venez de recevoir ' . $transaction_dto->montant . 'PO de la part de ' . $trans->emetteur_email . '.'
            );
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." non trouvé", $e->getEntity());
        } catch (SendMailException) {
            throw new SendMailException();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->toDTO($trans);
    }

    public function rechargerCompte($rechargement_dto): TransactionDTO{
        try{
            $trans = $this->transaction_repository->rechargerCompte($rechargement_dto->id_recepteur, $rechargement_dto->montant);
            $this->serviceLog->creationLogTransaction($trans->emetteur_id, $trans->id, $trans->montant);
            $this->serviceLog->creationLogReceptionTransaction($trans->emetteur_id, $trans->id);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." non trouvé", $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->toDTO($trans);
    }

    private function toDTO($trans): TransactionDTO
    {
        return new TransactionDTO(
            id: $trans->id,
            montant: $trans->montant,
            hash: $trans->hash,
            emetteur_id: $trans->emetteur_id,
            recepteur_id: $trans->recepteur_id,
            description: $trans->description,
            created_at: $trans->created_at
        );
    }

    private function toDTOAvecNom($trans,$user_emetteur, $user_recepteur): TransactionWithNameDTO
    {
        return new TransactionWithNameDTO(
            id: $trans->id,
            montant: $trans->montant,
            hash: $trans->hash,
            emetteur_id: $trans->emetteur_id,
            emetteur_nom: $user_emetteur->nom,
            emetteur_prenom:$user_emetteur->prenom,
            recepteur_id: $trans->recepteur_id,
            recepteur_nom: $user_recepteur->nom,
            recepteur_prenom: $user_recepteur->prenom,
            description: $trans->description,
            created_at: $trans->created_at
        );
    }
}