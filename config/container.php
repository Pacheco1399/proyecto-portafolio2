<?php

use App\Core\DatabaseConnection;
use App\Utils\DatabaseUtils;
use App\Service\SessionService;
use League\Config\Configuration;
use League\Config\ConfigurationInterface;
use League\Container\Container;
use League\Plates\Engine;

// Para definir las clases compartidas en un sólo lugar
$addSharedDefinitions = function (Container $container, ConfigurationInterface $config): void {
    $container->addShared(ConfigurationInterface::class, $config);

    // Motor de plantillas
    $container->addShared(Engine::class)->addArguments([
        $config->get('templates.path'),
        $config->get('templates.extension'),
    ]);



    // Gestor de conexión a la BD
    $container->addShared(DatabaseConnection::class)->addArguments([
        $config->get('database.driver'),
        $config->get('database.host'),
        $config->get('database.database'),
        $config->get('database.username'),
        $config->get('database.password'),
    ]);


    // Clases de utilidad
    $container->addShared(DatabaseUtils::class);
};



// Configura los servicios compartidos y habilita el auto-wiring en el resto de
// servicios para no tener que configurarlos manualmente
return function (Container $container, Configuration $config) use (
    $addSharedDefinitions,
): void {
    // Define las clases que requieren configuración adicional
    $addSharedDefinitions($container, $config);

    // Activa el auto-wiring para no tener que configurar manualmente nuevas clases
    $container->delegate(
        (new League\Container\ReflectionContainer())/*->cacheResolutions()*/,
    );
};
