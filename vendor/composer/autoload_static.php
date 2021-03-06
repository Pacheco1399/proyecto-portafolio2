<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5c4c809956eeecd93c8f2364578953c7
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'League\\Plates\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'League\\Plates\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/plates/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5c4c809956eeecd93c8f2364578953c7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5c4c809956eeecd93c8f2364578953c7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5c4c809956eeecd93c8f2364578953c7::$classMap;

        }, null, ClassLoader::class);
    }
}
