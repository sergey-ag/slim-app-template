<?php

use Respect\Validation\Validator as RespectValidator;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'slim',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => ''
        ]
    ]
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['auth'] = function ($container) {
    return new \Craftworks\App\Auth\Auth();
};

$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages();
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resourses/views/', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->getEnvironment()->addGlobal('auth', [
       'check' => $container->auth->check(),
       'user' => $container->auth->user()
    ]);
    $view->getEnvironment()->addGlobal('flash', $container->flash);
    return $view;
};

$container['validator'] = function ($container) {
    return new \Craftworks\App\Validation\Validator();
};

$container['csrfGuard'] = function ($container) {
    return new \Slim\Csrf\Guard();
};

$container['HomeController'] = function ($container) {
    return new \Craftworks\App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new \Craftworks\App\Controllers\AuthController($container);
};

$app->add(new \Craftworks\App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \Craftworks\App\Middleware\OldInputMiddleware($container));
$app->add(new \Craftworks\App\Middleware\CsrfGuardMiddleware($container));

$app->add($container->csrfGuard);

RespectValidator::with('Craftworks\\App\\Validation\\Rules\\');

require __DIR__ . '/routes.php';
