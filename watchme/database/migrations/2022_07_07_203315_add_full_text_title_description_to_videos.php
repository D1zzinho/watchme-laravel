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
        Schema::table('videos', function (Blueprint $table) {
            $table->fullText('title');
            $table->fullText('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropFullText('title');
            $table->dropFullText('description');
        });
    }
};
