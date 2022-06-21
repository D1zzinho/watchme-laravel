<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VideoUnauthorizedTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->video = $this->createVideo();
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_guests_cannot_access_single_video(): void
    {
        $this->withExceptionHandling();

        $this->getJson(route('videos.show', $this->video->hash_id))
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
