<?php

// Devuelve todas las configuraciones de la aplicación
// Debe cumplir con el formato definido en `_schema.php`
return [
    'database' => require __DIR__ . '/database.php',
    'container' => require __DIR__ . '/container.php',
    'router' => require __DIR__ . '/routes.php',
    'templates' => require __DIR__ . '/templates.php',
];
