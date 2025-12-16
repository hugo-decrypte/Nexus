<?php

use Psr\Container\ContainerInterface;
use nexus\core\application\usecases\AuthnProvider;
use nexus\core\application\usecases\interfaces\AuthnProviderInterface;
use nexus\infra\repositories\interface\AuthnRepositoryInterface;
use nexus\infra\repositories\PDOAuthnRepository;

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

];

