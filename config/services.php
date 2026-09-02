<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('RamElectronic\\DataTableBundle\\', __DIR__.'/../src/')
        ->exclude([
            __DIR__.'/../src/DataTableBundle.php',
            __DIR__.'/../src/DependencyInjection/',
            __DIR__.'/../src/Application/',
        ]);
};
