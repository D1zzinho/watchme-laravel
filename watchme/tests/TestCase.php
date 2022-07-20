<?php

namespace Tests;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function createUser()
    {
        return User::factory()->create();
    }

    public function authUser()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        return $user;
    }

    public function createVideo()
    {
        $user = $this->createUser();

        return $user->videos()->create(Video::factory()->make()->toArray());
    }

    public function createVideoForAuthenticatedUser()
    {
        $user = Auth::user();

        return $user->videos()->create(Video::factory()->make()->toArray());
    }
}
