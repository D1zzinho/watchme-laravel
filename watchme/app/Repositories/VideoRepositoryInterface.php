<?php

namespace App\Repositories;

use App\Http\Requests\StoreVideoRequest;
use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface VideoRepositoryInterface
{
    public function findAll(): Collection;
    public function findLatest(Request $request): Collection;
    public function suggest(): Collection;
    public function search(Request $request): LengthAwarePaginator;
    public function findByHashId(string $hashId): ?Video;
    public function store(StoreVideoRequest $request): ?Video;
}
