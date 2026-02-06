<?php

declare(strict_types=1);

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceLogInterface;
use PHPUnit\Framework\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogsListAction
{
    private ServiceLogInterface $logService;

    public function __construct(ServiceLogInterface $logService)
    {
        $this->logService = $logService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $logs = $this->logService->getLogs();
            $body = array_map(function ($log) {
                return [
                    'id' => $log->id,
                    'created_at' => $log->created_at,
                    'acteur_id' => $log->acteur_id,
                    'action_type' => $log->action_type,
                    'details' => $log->details
                ];
            }, $logs);

            $response->getBody()->write(json_encode($body));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
