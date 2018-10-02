<?php

namespace Craftworks\App\Models;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function setPassword($password)
    {
        $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}
