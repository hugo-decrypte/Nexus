<?php

use api\actions\AdminLogsAction;
use api\actions\ConnexionAction;
use api\actions\CreateTransactionAction;
use api\actions\EnregistrerAction;
use api\actions\LogsListAction;
use api\actions\TransactionByIdAction;
use api\actions\TransactionsAction;
use api\actions\TransactionsBetweenAction;
use api\actions\UserByIdAction;
use api\actions\UserSoldeAction;
use api\actions\DeleteUserAction;
use api\actions\UsersListAction;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\application\usecases\interfaces\ServiceLogInterface;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use Psr\Container\ContainerInterface;

return [
    // application
    AdminLogsAction::class => function (ContainerInterface $c) {
        return new AdminLogsAction($c->get('nexus.pdo'));
    },
    ConnexionAction::class => function (ContainerInterface $c) {
        return new ConnexionAction($c->get(ServiceAuthnInterface::class));
    },
    EnregistrerAction::class => function (ContainerInterface $c) {
        return new EnregistrerAction($c->get(ServiceAuthnInterface::class));
    },
    TransactionByIdAction::class => function (ContainerInterface $c) {
        return new TransactionByIdAction($c->get(ServiceTransactionInterface::class));
    },
    CreateTransactionAction::class => function (ContainerInterface $c) {
        return new CreateTransactionAction($c->get(ServiceTransactionInterface::class));
    },
    TransactionsAction::class => function (ContainerInterface $c) {
        return new TransactionsAction($c->get(ServiceTransactionInterface::class));
    },
    TransactionsBetweenAction::class => function (ContainerInterface $c) {
        return new TransactionsBetweenAction($c->get(ServiceTransactionInterface::class));
    },
    UserByIdAction::class => function (ContainerInterface $c) {
        return new UserByIdAction($c->get(ServiceAuthnInterface::class));
    },
    UserSoldeAction::class => function (ContainerInterface $c) {
        return new UserSoldeAction($c->get(ServiceTransactionInterface::class));
    },
    UsersListAction::class => function (ContainerInterface $c) {
        return new UsersListAction($c->get(ServiceAuthnInterface::class));
    },
    DeleteUserAction::class => function (ContainerInterface $c) {
        return new DeleteUserAction($c->get(ServiceAuthnInterface::class));
    },
    LogsListAction::class =>function(ContainerInterface $c) {
        return new LogsListAction($c->get(ServiceLogInterface::class));
    },
];

