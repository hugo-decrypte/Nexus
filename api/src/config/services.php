<?php


use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use infrastructure\repositories\PDOAuthnRepository;
use infrastructure\repositories\PDOTransactionRepository;
use Psr\Container\ContainerInterface;

return [

    AuthnRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthnRepository($c->get("auth.pdo"));
    },

    TransactionRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOTransactionRepository($c->get("auth.pdo"));
    }

    TransactionRepositoryInterface::class => function(ContainerInterface $c){
        return new PDOTransactionRepository($c->get(TransactionRepositoryInterface::class));
    }

];

