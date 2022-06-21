<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('playlist_video', function (Blueprint $table) {
            $table
                ->foreignId('playlist_id')
                ->constrained('playlists')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table
                ->foreignId('video_id')
                ->constrained('videos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();

            $table->primary(['playlist_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists_videos');
    }
};
