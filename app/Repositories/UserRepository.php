<?php

namespace App\Repositories;

use App\Events\UserRegisteredEvent;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create ($data): User
    {
        $user = User::create($data);
        event(new UserRegisteredEvent($user));
        return $user;
    }

    public function findByEmail ($email): User
    {
        return User::where('email', $email)->first();
    }
}
