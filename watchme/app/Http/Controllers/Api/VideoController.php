<?php

namespace App\Http\Controllers\Api;

use App\Helpers\VideoStream;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Repositories\VideoRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends BaseController
{
    private VideoRepositoryInterface $repository;

    public function __construct(VideoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $videos = $this->repository->findAll();

        return $this->sendResponse(VideoResource::collection($videos), 'Videos retrieved successfully.');
    }

    /**
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function latest(Request $request): JsonResponse
    {
        $videos = $this->repository->findLatest($request);

        return $this->sendResponse(VideoResource::collection($videos), 'Videos retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreVideoRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreVideoRequest $request): JsonResponse
    {
        $video = $this->repository->store($request);

        return $this->sendResponse(new VideoResource($video), 'Video successfully stored in database.')
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Video $video
     *
     * @return JsonResponse
     */
    public function show(Video $video): JsonResponse
    {
        $video->load('status', 'user', 'sources', 'tags');

        return $this->sendResponse(new VideoResource($video), 'Video retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Video $video
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateVideoRequest $request
     * @param  Video              $video
     *
     * @return JsonResponse
     */
    public function update(UpdateVideoRequest $request, Video $video): JsonResponse
    {
        $validated = $request->validated();

        $video->update($validated);
        $video->refresh();

        return $this->sendResponse(new VideoResource($video), 'Video successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Video $video
     *
     * @return JsonResponse
     */
    public function destroy(Video $video): JsonResponse
    {
        $isVideoDeleted = $video->delete();

        return $this->sendResponse($isVideoDeleted, 'Video successfully deleted.')
            ->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $videos = $this->repository->search($request);

        return $this->sendResponse($videos, "Found {$videos->total()} videos.");
    }

    /**
     * Stream the specified resource.
     *
     * @param  Video $video
     * @param  int   $quality
     *
     * @return StreamedResponse|JsonResponse
     */
    public function stream(Video $video, int $quality): StreamedResponse|JsonResponse
    {
        $video->load('sources');
        $source = $video->sources?->first(fn($source) => $source->type === $quality);

        if ($source && Storage::exists($source->pivot->source_path)) {
            $stream = new VideoStream($source->pivot->source_path);
            return response()->stream(
                function () use ($stream) {
                    $stream->start();
                }
            );
        }

        return $this->sendError('Video does not exists');
    }

    /**
     * Show the specified video thumbnail image.
     *
     * @param  Video $video
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws BindingResolutionException
     */
    public function thumbnail(Video $video)
    {
        $thumbnail = $video->thumbnail;

        if (!$video->exists || !Storage::exists($thumbnail)) {
            return $this->sendError('Video does not exists');
        }

        return $this->readMaterial($thumbnail);
    }

    /**
     * @param  Video $video
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws BindingResolutionException
     */
    public function preview(Video $video)
    {
        $preview = $video->preview;

        if (!$video->exists || !Storage::exists($preview)) {
            return $this->sendError('Video does not exists');
        }

        return $this->readMaterial($preview);
    }

    /**
     * @param  string $filepath
     *
     * @return \Illuminate\Http\Response
     * @throws BindingResolutionException
     */
    private function readMaterial(string $filepath): \Illuminate\Http\Response
    {
        $file = Storage::get($filepath);
        $type = Storage::mimeType($filepath);

        return response()->make($file, 200)->header('Content-Type', $type);
    }
}
