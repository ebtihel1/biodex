<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = app('router')->getRoutes();
$ref = new ReflectionProperty($routes, 'nameList');
$ref->setAccessible(true);
$names = $ref->getValue($routes);
foreach ($names as $name => $route) {
    if (stripos($name, 'dash') !== false || stripos($name, 'back') === 0) {
        echo sprintf("%-40s %s\n", $name, $route->uri());
    }
}
echo '--- routes with DashboardController action:' . PHP_EOL;
foreach ($routes->getRoutes() as $route) {
    if (str_contains($route->getActionName(), 'DashboardController')) {
        echo $route->uri() . ' name=' . var_export($route->getName(), true) . PHP_EOL;
    }
}
