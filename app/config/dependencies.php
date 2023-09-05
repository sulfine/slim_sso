<?php

declare(strict_types=1);

use Monolog\Logger;
use Slim\Views\Twig;
use DI\ContainerBuilder;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use App\Common\Architecture\Config\ModuleConfig;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'logger' => function (ContainerInterface $container) {
            $settings = $container->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        'entity_manager' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            $connection = $container->get('db');

            $configurationORM = new Configuration;
            $driverImpl = new AttributeDriver(ModuleConfig::getEntitiesModules(), true);

            if ($settings['debug']) {
                $queryCache = new ArrayAdapter();
                $metadataCache = new ArrayAdapter();
                $configurationORM->setAutoGenerateProxyClasses(true);
                /*} else { //TODO Implementation mode prod pour le cache entity_manager
                $queryCache = new PhpFilesAdapter('doctrine_queries');
                $metadataCache = new PhpFilesAdapter('doctrine_metadata');
                $config->setAutoGenerateProxyClasses(false);*/
            }

            $configurationORM->setMetadataCache($metadataCache);
            $configurationORM->setQueryCache($queryCache);
            $configurationORM->setMetadataDriverImpl($driverImpl);
            $configurationORM->setProxyDir($settings['doctrine']['meta']['proxy_dir']);
            $configurationORM->setProxyNamespace('App\Proxies');

            return new EntityManager($connection, $configurationORM);
        },
        'db' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            return DriverManager::getConnection($settings['doctrine']['connection']);
        },
        'session' => function (ContainerInterface $container) {
            return new \App\Common\Middleware\SessionMiddleware;
        },
        'flash' => function (ContainerInterface $container) {
            $session = $container->get('session');
            return new \Slim\Flash\Messages($session);
        },
        'twig_profile' => function () {
            return new \Twig\Profiler\Profile();
        },
        'view' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            return Twig::create($settings['view']['template_path'], $settings['view']['twig']);
        },
    ]);
};
