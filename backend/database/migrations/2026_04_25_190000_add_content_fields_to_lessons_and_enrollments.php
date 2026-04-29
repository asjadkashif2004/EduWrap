<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('title');
            $table->string('notes_url')->nullable()->after('video_url');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->json('completed_lesson_ids')->nullable()->after('completed_lessons');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['completed_lesson_ids']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'notes_url']);
        });
    }
};
