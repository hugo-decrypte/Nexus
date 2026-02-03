<?php

use api\actions\ConnexionAction;
use api\actions\EnregistrerAction;
use api\actions\TransactionAction;
use api\actions\TransactionsAction;
use api\actions\UserByIdAction;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use Psr\Container\ContainerInterface;

return [
    // application
    ConnexionAction::class => function (ContainerInterface $c) {
        return new ConnexionAction($c->get(ServiceAuthnInterface::class));
    },
    EnregistrerAction::class => function (ContainerInterface $c) {
        return new EnregistrerAction($c->get(ServiceAuthnInterface::class));
    },
    TransactionAction::class => function (ContainerInterface $c) {
        return new TransactionAction($c->get(ServiceTransactionInterface::class));
    },
    TransactionsAction::class => function (ContainerInterface $c) {
        return new TransactionsAction($c->get(ServiceTransactionInterface::class));
    },
    UserByIdAction::class => function (ContainerInterface $c) {
        return new UserByIdAction($c->get(AuthnRepositoryInterface::class));
    },
];

