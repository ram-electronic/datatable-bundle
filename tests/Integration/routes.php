<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('test_list', '/list')->methods(['GET']);
    $routes->import('@LiveComponentBundle/config/routes.php');
};
