<?php


use application_core\application\usecases\AuthnProvider;
use application_core\application\usecases\interfaces\AuthnProviderInterface;
use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\application\usecases\interfaces\ServiceLogInterface;
use application_core\application\usecases\interfaces\ServiceTransactionInterface;
use application_core\application\usecases\ServiceAuthn;
use application_core\application\usecases\ServiceTransaction;
use infrastructure\repositories\interfaces\AuthnRepositoryInterface;
use infrastructure\repositories\interfaces\LogRepositoryInterface;
use infrastructure\repositories\interfaces\TransactionRepositoryInterface;
use infrastructure\repositories\PDOAuthnRepository;
use infrastructure\repositories\PDOLogRepository;
use infrastructure\repositories\PDOTransactionRepository;
use Psr\Container\ContainerInterface;

return [

    AuthnRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthnRepository($c->get("nexus.pdo"));
    },

    TransactionRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOTransactionRepository($c->get("nexus.pdo"), $c->get(AuthnRepositoryInterface::class));
    },
    LogRepositoryInterface::class => function (ContainerInterface $c){
        return new PDOLogRepository($c->get("nexus_pdo"));
    },
    AuthnProviderInterface::class => function (ContainerInterface $c) {
        return new AuthnProvider($c->get(AuthnRepositoryInterface::class));
    },

    ServiceTransactionInterface::class => function (ContainerInterface $c) {
        return new ServiceTransaction($c->get(TransactionRepositoryInterface::class));
    },
    ServiceAuthnInterface::class => function (ContainerInterface $c) {
        return new ServiceAuthn($c->get(AuthnProviderInterface::class), $c->get(AuthnRepositoryInterface::class),parse_ini_file($c->get('db.config'))["JWT_SECRET"]);
    },
    ServiceLogInterface::class => function (ContainerInterface $c) {
        return new ServiceTransaction($c->get(TransactionRepositoryInterface::class));
    },


];

