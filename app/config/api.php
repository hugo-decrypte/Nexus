<?php

use Psr\Container\ContainerInterface;
use nexus\api\actions\EnregistrerAction;
use nexus\api\actions\ConnexionAction;
use nexus\api\middlewares\AuthnSigninValidationMiddleware;
use nexus\api\middlewares\JwtAuthMiddleware;
use nexus\core\application\usecases\interfaces\ServiceAuthnInterface;

return [
    // application
//    Action::class=> function (ContainerInterface $c) {
//        return new Action($c->get(ServiceInterface::class));
//    },

    ConnexionAction::class=> function (ContainerInterface $c) {
        return new ConnexionAction($c->get(ServiceAuthnInterface::class));
    },

    EnregistrerAction::class=> function (ContainerInterface $c) {
        return new EnregistrerAction($c->get(ServiceAuthnInterface::class));
    },

    AuthnSigninValidationMiddleware::class => function (ContainerInterface $c) {
        return new AuthnSigninValidationMiddleware();
    },

    JwtAuthMiddleware::class => function (ContainerInterface $c) {
        return new JwtAuthMiddleware(parse_ini_file($c->get('db.config'))["JWT_SECRET"]);
    }
];

