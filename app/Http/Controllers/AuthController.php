<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $token = $this->authService->register($request->validated());
        return $this->successResponse(
            data: ['token' => $token],
            message: 'Registered successfully',
            code: 201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());
        return $this->successResponse(
            data: ['token' => $token],
            message: 'Logged in successfully',
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout(auth()->user());
        return $this->successResponse(
            message: 'Logged out successfully'
        );
    }
}
