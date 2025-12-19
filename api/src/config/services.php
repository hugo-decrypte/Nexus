<?php

use application_core\application\usecases\AuthnProvider;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use infrastructure\repositories\PDOAuthnRepository;
use infrastructure\repositories\PDOTransactionRepository;
use Psr\Container\ContainerInterface;

return [
    // SERVICES
//    RepositoryInterface::class => function (ContainerInterface $c) {
//        return new PDORepository($c->get("nexus.pdo"));
//    },

    AuthnRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthnRepository($c->get("auth.pdo"));
    },

    AuthnProviderInterface::class => function (ContainerInterface $c) {
        return new AuthnProvider($c->get(AuthnRepositoryInterface::class));
    },

    TransactionRepositoryInterface::class => function(ContainerInterface $c){
        return new PDOTransactionRepository($c->get(TransactionRepositoryInterface::class));
    }

];

