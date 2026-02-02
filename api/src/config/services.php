<?php


use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use application_core\application\usecases\ServiceTransaction;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use infrastructure\repositories\PDOAuthnRepository;
use infrastructure\repositories\PDOTransactionRepository;
use Psr\Container\ContainerInterface;

return [

    AuthnRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthnRepository($c->get("nexus.pdo"));
    },

    TransactionRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOTransactionRepository($c->get("nexus.pdo"));
    },

    ServiceTransactionInterface::class => function (ContainerInterface $c) {
        return new ServiceTransaction($c->get(TransactionRepositoryInterface::class));
    },

];

