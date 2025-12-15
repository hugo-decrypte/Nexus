<?php
declare(strict_types=1);

use nexus\api\actions\EnregistrerAction;
use nexus\api\actions\ConnexionAction;
use nexus\api\middlewares\AuthnSigninValidationMiddleware;
use nexus\api\middlewares\EnregistrerUtilisateurMiddleware;
use Slim\App;


return function( App $app): App {
    $app->post("/signin", ConnexionAction::class)
        ->add(AuthnSigninValidationMiddleware::class);
    $app->post('/register', EnregistrerAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    return $app;
};