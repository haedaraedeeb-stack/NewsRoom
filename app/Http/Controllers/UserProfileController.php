<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfile\StoreUserProfileRequest;
use App\Http\Requests\UserProfile\UpdateUserProfileRequest;
use App\Services\UserProfileService;
use Illuminate\Http\JsonResponse;

class UserProfileController extends Controller
{
    public function __construct(
        private readonly UserProfileService $userProfileService
    ) {}

    public function show(): JsonResponse
    {
        $profile = $this->userProfileService->getProfile(auth()->id());

        return $this->successResponse(
            data: ['profile' => $profile],
            message: 'Profile retrieved successfully.',
        );
    }

    public function store(StoreUserProfileRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->createProfile(
            auth()->id(),
            $request->validated(),
            $request->file('attachment')
        );
        return $this->successResponse(
            data: ['profile' => $profile],
            message: 'Profile saved successfully.',
            code: 201,
        );
    }

    public function update(UpdateUserProfileRequest $request): JsonResponse
    {
        $profile = $this->userProfileService->updateProfile(
            auth()->id(),
            $request->validated()
        );

        return $this->successResponse(
            data: ['profile' => $profile],
            message: 'Profile updated successfully.',
        );
    }
}
