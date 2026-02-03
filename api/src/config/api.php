<?php

use api\actions\ConnexionAction;
use api\actions\EnregistrerAction;
use api\actions\TransactionAction;
use api\actions\TransactionsAction;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use Psr\Container\ContainerInterface;

return [
    // application
    ConnexionAction::class=> function (ContainerInterface $c) {
        return new ConnexionAction($c->get(ServiceAuthnInterface::class));
    },
    EnregistrerAction::class=> function (ContainerInterface $c) {
        return new EnregistrerAction($c->get(ServiceAuthnInterface::class));
    },
    TransactionAction::class=> function (ContainerInterface $c) {
        return new TransactionAction($c->get(ServiceTransactionInterface::class));
    },
    TransactionsAction::class=> function (ContainerInterface $c) {
        return new TransactionsAction($c->get(ServiceTransactionInterface::class));
    }
];

