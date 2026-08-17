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
            // Excluded because they collide by component name with the consuming app's own
            // anonymous Table/Row/Cell/Head/Header/Pagination Twig components - only the
            // non-colliding parts of Presentation/Twig/Components (FilterForm, FilterCondition,
            // PaginationControl, Table/ActionsCell, Table/DataCell, Table/HeadRow,
            // Table/SortableHeader) are registered.
            __DIR__.'/../src/Presentation/Twig/Components/Table.php',
            __DIR__.'/../src/Presentation/Twig/Components/Table/Body.php',
            __DIR__.'/../src/Presentation/Twig/Components/Table/Cell.php',
            __DIR__.'/../src/Presentation/Twig/Components/Table/Head.php',
            __DIR__.'/../src/Presentation/Twig/Components/Table/Header.php',
            __DIR__.'/../src/Presentation/Twig/Components/Table/Row.php',
            __DIR__.'/../src/Presentation/Twig/Components/Pagination.php',
        ]);
};
