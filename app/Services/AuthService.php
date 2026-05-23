<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepositoryInterface
    ) {}

    public function register(array $data): string
    {
        $user = $this->userRepositoryInterface->create($data);
        $user->assignRole('reader');
        return $user->createToken('api')->plainTextToken;
    }

    public function login(array $data): string
    {
        $user = $this->userRepositoryInterface->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('The provided credentials are incorrect');
        }

        return $user->createToken('api')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
