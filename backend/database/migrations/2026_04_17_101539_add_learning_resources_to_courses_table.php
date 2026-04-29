<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('level')->default('Beginner')->after('category');
            $table->string('thumbnail_url')->nullable()->after('description');
            $table->string('documentation_url')->nullable()->after('thumbnail_url');
            $table->string('youtube_url')->nullable()->after('documentation_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'thumbnail_url', 'documentation_url', 'youtube_url']);
        });
    }
};
