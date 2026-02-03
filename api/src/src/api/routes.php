<?php
declare(strict_types=1);

use api\actions\ConnexionAction;
use api\actions\EnregistrerAction;
use api\actions\TransactionAction;
use api\actions\TransactionsAction;
use api\actions\UserByIdAction;
use api\middlewares\EnregistrerUtilisateurMiddleware;
use api\middlewares\AuthnConnexionValidationMiddleware;
use Slim\App;


return function( App $app): App {
    $app->get('/health', function ($request, $response) {
        $response->getBody()->write(json_encode(['status' => 'ok']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });
    $app->get('/users/{id_user}', UserByIdAction::class);
    $app->get("/transactions/{id_user}", TransactionAction::class);
    $app->get("/transactions", TransactionsAction::class);
    $app->post("/signin", ConnexionAction::class)
        ->add(AuthnConnexionValidationMiddleware::class);
    $app->post('/register', EnregistrerAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    return $app;
};