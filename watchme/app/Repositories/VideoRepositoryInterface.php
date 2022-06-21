<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Support\Collection;

interface VideoRepositoryInterface
{
    public function findAll(): Collection;
    public function findByHashId(string $hashId): ?Video;
}
