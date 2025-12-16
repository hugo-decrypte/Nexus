<?php
declare(strict_types=1);

use api\actions\ConnexionAction;
use api\actions\EnregistrerAction;
use api\middlewares\EnregistrerUtilisateurMiddleware;
use nexus\api\middlewares\AuthnConnexionValidationMiddleware;
use Slim\App;


return function( App $app): App {
    $app->post("/signin", ConnexionAction::class)
        ->add(AuthnConnexionValidationMiddleware::class);
    $app->post('/register', EnregistrerAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    return $app;
};