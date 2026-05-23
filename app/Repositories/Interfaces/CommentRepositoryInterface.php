<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;
use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    public function getByModel(string $type, int $id): Collection;
    public function create(array $data): Comment;
    public function update(int $id, array $data): Comment;
    public function delete(int $id): bool;
    public function findById(int $id): Comment;
}
