<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf4c79a541184febc2f79d8ca213011d5
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Devmohy\\Budpay\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Devmohy\\Budpay\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf4c79a541184febc2f79d8ca213011d5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf4c79a541184febc2f79d8ca213011d5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf4c79a541184febc2f79d8ca213011d5::$classMap;

        }, null, ClassLoader::class);
    }
}
