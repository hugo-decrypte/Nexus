<?php
namespace application_core\application\usecases;


use api\dtos\LogDTO;
use application_core\application\usecases\interfaces\ServiceLogInterface;
use application_core\exceptions\EntityNotFoundException;
use infrastructure\repositories\interfaces\LogRepositoryInterface;

class ServiceLog implements ServiceLogInterface {

    private LogRepositoryInterface $log_repository;

    public function __construct(LogRepositoryInterface $log_repository) {
        $this->log_repository = $log_repository;
    }

    public function getLogByActeur(string $id_user): array
    {
        try {
            $logs = $this->log_repository->getLogByActeur($id_user);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return array_map(fn ($log) => $this->toDTO($log), $logs);
    }

    public function getLogById(string $id): LogDTO
    {
        try {
            $trans = $this->log_repository->getLogById($id);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getMessage(), $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $this->toDTO($trans);
    }

    public function getLogs(): array
    {
        try {
            $logs = $this->log_repository->getLogs();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return array_map(fn ($log) => $this->toDTO($log), $logs);
    }

    public function creationLogTransaction(string $acteur_id,string $id_transaction, int $montant): void{
        try {
            $this->log_repository->creationLogTransaction($acteur_id, $id_transaction, $montant);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
    public function creationLogReceptionTransaction (string $acteur_id,string $id_transaction): void{
        try {
            $this->log_repository->creationLogReceptionTransaction($acteur_id, $id_transaction);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
    public function creationLogConnection (string $acteur_id): void{
        try {
            $this->log_repository->creationLogConnection($acteur_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
    private function toDTO($log): LogDTO
    {
        return new LogDTO(
            id: $log->id,
            created_at: $log->created_at,
            acteur_id: $log->acteur_id,
            action_type: $log->action_type,
            details: $log->details
        );
    }
}