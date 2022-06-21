<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hash_id'     => Str::random(15),
            'title'       => Str::random(100),
            'description' => Str::random(255),
            'thumbnail'   => $this->faker->filePath(),
            'preview'     => $this->faker->filePath(),
            'height'      => Collection::make([360, 720, 1080])->random(),
            'created_at'  => Carbon::now()
        ];
    }
}
