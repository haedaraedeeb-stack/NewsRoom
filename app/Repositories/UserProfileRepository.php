<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\UserProfile;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?UserProfile
    {
        return UserProfile::where('user_id', $userId)->first();
    }

    public function create(array $data): UserProfile
    {
        return UserProfile::create($data);
    }

    public function update(UserProfile $profile, array $data): UserProfile
    {
        $profile->update($data);
        return $profile;
    }
}
