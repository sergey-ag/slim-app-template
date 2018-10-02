<?php

namespace Craftworks\App\Controllers;

use Craftworks\App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends \Craftworks\App\Controllers\Controller
{
    public function getChangePassword($request, $response)
    {
        return $this->view->render($response, 'auth/change-password.twig');
    }

    public function postChangePassword($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'currentPassword' => v::noWhitespace()->notEmpty()->matchesPassword($this->auth->user()->password),
            'password' => v::noWhitespace()->notEmpty()
        ]);
        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.change-password'));
        }

        $this->auth->user()->setPassword($request->getParam('password'));

        $this->flash->addMessage('info', 'Your password has been changed');

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getSignOut($request, $response)
    {
        $this->auth->logout();
        $this->flash->addMessage('info', 'You have been signed out!');
        return $response->withRedirect($this->router->pathFor('home'));
    }
    
    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'auth/signin.twig');
    }

    public function postSignIn($request, $response)
    {
        $user = $request->getParam('user');
        $auth = $this->auth->attempt($user['email'], $user['password']);

        if (!$auth) {
            $this->flash->addMessage('error', 'Could not sign you in with those details.');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }
    
    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'auth/signup.twig');
    }

    public function postSignUp($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::notEmpty()->noWhitespace()->email()->emailAvailable(),
            'name' => v::notEmpty()->alpha(),
            'password' => v::notEmpty()->noWhitespace()
        ]);
        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $user = User::create([
            'email' => $request->getParam('email'),
            'name' => $request->getParam('name'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT)
        ]);

        $this->flash->addMessage('info', 'You have been signed up!');

        $this->auth->attempt($user->email, $request->getParam('password'));

        return $response->withRedirect($this->router->pathFor('home'));
    }
}
