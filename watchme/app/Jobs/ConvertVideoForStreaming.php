<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\AdvancedMedia;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;
use ProtoneMedia\LaravelFFMpeg\Filters\TileFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Video $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sourceRoot = "v2/{$this->video->hash_id}/{$this->video->hash_id}";
        $thumbnailSource = "{$sourceRoot}_thumbnail.jpeg";
        $sourceFor360p = "{$sourceRoot}_360.mp4";
        $sourceFor720p = "{$sourceRoot}_720.mp4";
        $sourceForPreview = "{$sourceRoot}_preview.webm";

        $format = new X264();

        $media = FFMpeg::fromDisk('media_storage')
            ->open($this->video->sources[0]->pivot->source_path);
        $duration = $media->getDurationInSeconds();
        $dimensions = $media->getVideoStream()->getDimensions();

        $interval = $duration < 900 ? 45 : 150;

        $media
            ->export()
            ->resize(480, 360)
            ->toDisk('media_storage')
            ->inFormat($format)
            ->save($sourceFor360p)

            ->export()
            ->resize(1280, 720)
            ->toDisk('media_storage')
            ->inFormat($format)
            ->save($sourceFor720p)

            ->export()
            ->addFilter(['-vf', "select='lt(mod(t\,{$interval})\,0.8)',setpts=N/FRAME_RATE/TB", '-an'])
            ->toDisk('media_storage')
            ->save($sourceForPreview)

            ->getFrameFromSeconds((int)($duration / 5))
            ->export()
            ->toDisk('media_storage')
            ->save($thumbnailSource)

            ->exportTile(
                function (TileFactory $factory) {
                    $factory->interval(20)
                        ->scale(160, 90)
                        ->grid(5, 5)
                        ->generateVTT("{$this->video->hash_id}_thumbnails.vtt");
                }
            )
            ->toDisk('thumbnails_storage')
            ->save("{$this->video->hash_id}_tile_%05d.jpg");

        // update the database so we know the convertion is done!
        $this->video->update(
            [
                'thumbnails'   => "storage/videos/thumbnails/{$this->video->hash_id}_thumbnails.vtt",
                'poster'       => $thumbnailSource,
                'preview'      => $sourceRoot . '_preview.webm',
                'duration'     => $media->getDurationInSeconds(),
                'width'        => $dimensions->getWidth(),
                'height'       => $dimensions->getHeight(),
                'converted_at' => Carbon::now(),
            ]
        );

        $this->video->sources()
            ->syncWithoutDetaching(
                [
                    2 => ['source_path' => $sourceFor360p],
                    4 => ['source_path' => $sourceFor720p]
                ],
            );
    }
}
