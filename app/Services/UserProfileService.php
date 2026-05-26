<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use App\Services\AttachmentService;
class UserProfileService
{
    public function __construct(
        private readonly UserProfileRepositoryInterface $userProfileRepository,
        private readonly AttachmentService  $attachmentService
    ) {}

    public function getProfile(int $userId): ?UserProfile
    {
        return $this->userProfileRepository->findByUserId($userId);
    }

    public function createProfile(int $userId, array $data, UploadedFile $file = null): UserProfile
    {
        $profileData = array_merge($data, ['user_id' => $userId]);
        $profile = $this->userProfileRepository->create($profileData);
        if ($file)
        {
            $files = $this->attachmentService->store($profile, $file);
        }
        return $profile->load('attachments');
    }

    public function updateProfile(int $userId, array $data): UserProfile
    {
        $profile = $this->userProfileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ModelNotFoundException('User profile not found.');
        }
        return $this->userProfileRepository->update($profile, $data);
    }
}
