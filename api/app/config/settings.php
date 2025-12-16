<?php

use Psr\Container\ContainerInterface;
use nexus\api\actions\PraticiensAction;

return [
    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'db.config' => __DIR__ . '/.env',

    // infra
     'nexus.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('db.config'));
        $dsn = "{$config['nexus.driver']}:host={$config['nexus.host']};dbname={$config['nexus.database']}";
        $user = $config['nexus.username'];
        $password = $config['nexus.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },
];

