<?php

namespace Craftworks\App\Auth;

use Craftworks\App\Models\User;

class Auth
{
    public function logout()
    {
        unset($_SESSION['user']);
    }
    
    public function user()
    {
        if ($this->check()) {
            return User::find($_SESSION['user']);
        }
        return null;
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function attempt($email, $password)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }
        if (password_verify($password, $user->password)) {
            $_SESSION['user'] = $user->id;
            return true;
        }

        return false;
    }
}
