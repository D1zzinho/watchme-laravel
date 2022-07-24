<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('source_video', function (Blueprint $table) {
            $table
                ->foreignId('video_id')
                ->constrained('videos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table
                ->foreignId('source_id')
                ->constrained('sources')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('source_path', 255);
            $table->timestamps();

            $table->primary(['video_id', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sources_videos');
    }
};
