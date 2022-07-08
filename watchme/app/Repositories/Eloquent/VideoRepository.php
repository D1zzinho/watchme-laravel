<?php

namespace App\Repositories\Eloquent;

use App\Http\Requests\StoreVideoRequest;
use App\Jobs\ConvertVideoForDownloading;
use App\Jobs\ConvertVideoForStreaming;
use App\Models\Video;
use App\Repositories\VideoRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * @param  string $hashId
     *
     * @return Video|null
     */
    public function findByHashId(string $hashId): ?Video
    {
        return Video::whereHashId($hashId)->with('user', 'status', 'sources')->first();
    }

    /**
     * @param  StoreVideoRequest $request
     *
     * @return Video|null
     */
    public function store(StoreVideoRequest $request): ?Video
    {
        $user = Auth::user();

        $validated = $request->safe()->except(['file']);

        DB::beginTransaction();

        $video = $user->videos()->create($validated);

        $path = $request->file('file')->store('tmp', 'media_storage');
        $video->sources()->attach(1, ['source_path' => $path]);

        ConvertVideoForDownloading::dispatch($video);
        ConvertVideoForStreaming::dispatch($video);

        DB::commit();

        return $video;
    }

    /**
     * @param  Request $request
     *
     * @return Collection
     */
    public function findLatest(Request $request): Collection
    {
        $limit = $request->limit;

        return Video::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * @param  Request $request
     *
     * @return LengthAwarePaginator
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $query = $request->search;

        return Video::search($query)->paginate($request->limit ?: 20);
    }

    public function suggest(): Collection
    {
        // TODO: Implement suggest() method.
    }
}
