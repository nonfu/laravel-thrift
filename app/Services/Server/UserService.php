<?php
namespace App\Services\Server;

use App\Thrift\User\UserIf as UserIf;
use App\User;

class UserService implements UserIf
{
    public function getInfo($id)
    {
        return User::find($id);
    }
}