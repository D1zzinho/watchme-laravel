<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseTransactions;

    private Video $video;

    public function setUp(): void
    {
        parent::setUp();
        $this->authUser();
        $this->video = $this->createVideoForAuthenticatedUser();
    }

    public function test_get_all_videos(): void
    {
        $response = $this->getJson(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson(
                [
                    'success' => true
                ]
            );
    }

    public function test_get_single_video(): void
    {
        $this->getJson(route('videos.show', $this->video->hash_id))
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('data.title', $this->video->title);
    }

    public function test_store_new_video(): void
    {
        Storage::persistentFake('media_storage');

        $video = Video::factory()->make();
        $file = UploadedFile::fake()->create('video1.mp4', 2048, 'video/mp4');

        $response = $this->postJson(
            route('videos.store'),
            array_merge(
                $video->toArray(),
                ['file' => $file]
            )
        )
            ->assertCreated()
            ->json('data');

        $this->assertEquals($video->title, $response['title']);
        $this->assertDatabaseHas('videos', ['hash_id' => $video->hash_id, 'title' => $video->title]);

        Storage::disk('media_storage')->assertExists("tmp/{$file->hashName()}");
        $this->assertFileEquals($file, Storage::disk('media_storage')->path("tmp/{$file->hashName()}"));
    }

    public function test_while_storing_video_required_fields_are_present(): void
    {
        $this->withExceptionHandling();

        $this->postJson(route('videos.store'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['hash_id', 'title', 'description', 'thumbnail', 'preview', 'height']);
    }

    public function test_delete_video(): void
    {
        $this->deleteJson(route('videos.destroy', $this->video->hash_id))
            ->assertNoContent();

        $this->assertDatabaseMissing(
            'videos',
            [
                'hash_id' => $this->video->hash_id,
                'title'   => $this->video->title
            ]
        );
    }

    public function test_update_video(): void
    {
        $updateFormImitation = [
            'video_status_id' => 2,
            'title'           => 'Updated title',
            'description'     => 'Updated description',
            'views'           => $this->video->views + 1
        ];

        $this->patchJson(route('videos.update', $this->video->hash_id), $updateFormImitation)
            ->assertOk();

        $this->assertDatabaseHas(
            'videos',
            array_merge(['hash_id' => $this->video->hash_id], $updateFormImitation)
        );
    }
}
