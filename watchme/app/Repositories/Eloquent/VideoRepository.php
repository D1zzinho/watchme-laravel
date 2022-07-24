<?php

namespace App\Repositories\Eloquent;

use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Jobs\ConvertVideoForStreaming;
use App\Models\Tag;
use App\Models\Video;
use App\Repositories\VideoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $date = Carbon::now()->format('Y_m_d_H_i_s');

        $validated = $request->safe()->except(['file']);
        $validated['hash_id'] = Str::random(15);

        DB::beginTransaction();

        $video = $user->videos()->create($validated);

        $filename = Str::slug($video->title, '_') . '_' . $date . '.mp4';
        $path = $request->file('file')->storeAs(
            "v2/{$video->hash_id}",
            $filename,
            'media_storage'
        );

        $video->sources()->attach(1, ['source_path' => $path]);

        ConvertVideoForStreaming::dispatch($video);

        DB::commit();

        return $video;
    }

    /**
     * @param  UpdateVideoRequest $request
     * @param  Video              $video
     *
     * @return Video
     */
    public function update(UpdateVideoRequest $request, Video $video): Video
    {
        $validated = $request->validated();

        DB::beginTransaction();

        $video->update($validated);
        $video->refresh();

        if (isset($validated['tags'])) {
            $tags = [];
            foreach ($validated['tags'] as $tagName) {
                $slug = Str::slug($tagName);
                $tag = Tag::where('name', '=', $tagName)
                    ->firstOrCreate(
                        [
                            'name' => $tagName,
                            'slug' => $slug
                        ]
                    );
                $tags[] = $tag->id;
            }

            $video->tags()->syncWithoutDetaching($tags);
        }

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
