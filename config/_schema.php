<?php

use App\Core\DatabaseConnection;
use Nette\Schema\Expect;

// Estructura y tipos de valores esperados para la configuración
// Útil para saber qué tipo de datos se debe retornar en los distintos archivos
// de configuración
return [
    // Posee una estructura definida
    'database' => DatabaseConnection::getConfigSchema(),

    // Se espera que sean funciones, las cuales se encargarán de definir su
    // configuración respectiva
    'container' => Expect::type('callable'),
    'router' => Expect::type('callable'),

    // Se espera que sea un array simple, no se valida la estructura (se podría hacer)
    'templates' => Expect::array('templates'),
];
