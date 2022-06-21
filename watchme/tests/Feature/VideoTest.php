<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    /**
     * @test
     *
     * @return void
     */
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

    /**
     * @test
     *
     * @return void
     */
    public function test_get_single_video(): void
    {
        $this->getJson(route('videos.show', $this->video->hash_id))
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('data.title', $this->video->title);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_store_new_video(): void
    {
        $video = Video::factory()->make();

        $response = $this->postJson(route('videos.store'), $video->toArray())
            ->assertCreated()
            ->json('data');

        $this->assertEquals($video->title, $response['title']);
        $this->assertDatabaseHas('videos', ['hash_id' => $video->hash_id, 'title' => $video->title]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_while_storing_video_required_fields_are_present(): void
    {
        $this->withExceptionHandling();

        $this->postJson(route('videos.store'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['hash_id', 'title', 'description', 'thumbnail', 'preview', 'height']);
    }

    /**
     * @test
     *
     * @return void
     */
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

    /**
     * @test
     *
     * @return void
     */
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
