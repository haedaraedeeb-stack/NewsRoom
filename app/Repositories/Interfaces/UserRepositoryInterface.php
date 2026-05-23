<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use PhpParser\Builder\Interface_;

Interface UserRepositoryInterface
{
    public function create ($data): User;
    public function findByEmail ($email): ?User;
}
