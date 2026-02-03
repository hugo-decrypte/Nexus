<?php

declare(strict_types=1);

namespace api\actions;

use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminLogsAction
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stmt = $this->pdo->query(
            "SELECT id, acteur_id, action_type, details, date_creation FROM logs ORDER BY date_creation DESC"
        );
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($logs));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
