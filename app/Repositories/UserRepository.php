<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create ($data): User
    {
        return User::create($data);
    }

    public function findByEmail ($email): User
    {
        return User::where('email', $email)->first();
    }
}
