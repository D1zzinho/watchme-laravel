<?php

namespace App\Repositories\Eloquent;

use App\Models\Video;
use App\Repositories\VideoRepositoryInterface;
use Illuminate\Support\Collection;

class VideoRepository implements VideoRepositoryInterface
{
    /**
     * Gets all videos.
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return Video::all();
    }

    /**
     * Gets video by its hash id.
     *
     * @param string $hashId
     *
     * @return Video|null
     */
    public function findByHashId(string $hashId): ?Video
    {
        return Video::whereHashId($hashId)->with('user', 'status', 'sources')->first();
    }
}
