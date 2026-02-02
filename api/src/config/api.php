<?php

use api\actions\ConnexionAction;
use api\actions\EnregistrerAction;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use Psr\Container\ContainerInterface;

return [
    // application
    ConnexionAction::class=> function (ContainerInterface $c) {
        return new ConnexionAction($c->get(ServiceAuthnInterface::class));
    },
    EnregistrerAction::class=> function (ContainerInterface $c) {
        return new EnregistrerAction($c->get(ServiceAuthnInterface::class));
    }
];

