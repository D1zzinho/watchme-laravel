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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table
                ->foreignId('video_status_id')
                ->default(1)
                ->constrained('video_statuses')
                ->onUpdate('cascade');
            $table->string('hash_id', 20)->unique();
            $table->string('title', 100);
            $table->string('description', 500);
            $table->string('thumbnail', 255);
            $table->string('preview', 255);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height');
            $table->unsignedMediumInteger('duration')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
