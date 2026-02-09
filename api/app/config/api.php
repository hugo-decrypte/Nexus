<?php

use api\actions\AdminLogsAction;
use api\actions\SigninAction;
use api\actions\CreateTransactionAction;
use api\actions\RegisterAction;
use api\actions\LogsListAction;
use api\actions\TransactionByIdAction;
use api\actions\TransactionsAction;
use api\actions\TransactionsBetweenAction;
use api\actions\UserByIdAction;
use api\actions\UserSoldeAction;
use api\actions\DeleteUserAction;
use api\actions\UsersListAction;
use api\actions\UpdateUserAction;
use api\actions\UpdatePasswordAction;
use api\middlewares\authz\AuthzUserRessourceAccessMiddleware;
use api\middlewares\JwtAuthMiddleware;
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
    SigninAction::class => function (ContainerInterface $c) {
        return new SigninAction($c->get(ServiceAuthnInterface::class));
    },
    RegisterAction::class => function (ContainerInterface $c) {
        return new RegisterAction($c->get(ServiceAuthnInterface::class));
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
    UpdateUserAction::class => function (ContainerInterface $c) {
        return new UpdateUserAction($c->get(ServiceAuthnInterface::class));
    },
    UpdatePasswordAction::class => function (ContainerInterface $c) {
        return new UpdatePasswordAction($c->get(ServiceAuthnInterface::class));
    },
    LogsListAction::class =>function(ContainerInterface $c) {
        return new LogsListAction($c->get(ServiceLogInterface::class));
    },
    JwtAuthMiddleware::class => function (ContainerInterface $c) {
        return new JwtAuthMiddleware(parse_ini_file($c->get('db.config'))["JWT_SECRET"]);
    },
];

