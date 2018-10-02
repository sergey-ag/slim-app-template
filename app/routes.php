<?php

$app->get('/', 'HomeController:index')->setName('home');

$app->group('', function () {
    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp');

    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new \Craftworks\App\Middleware\GuestMiddleware($container));

$app->group('', function () {
    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/auth/change-password', 'AuthController:getChangePassword')->setName('auth.change-password');
    $this->post('/auth/change-password', 'AuthController:postChangePassword');
})->add(new \Craftworks\App\Middleware\AuthMiddleware($container));
