<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('geeksforgeeks_url')->nullable()->after('documentation_url');
            $table->string('w3schools_url')->nullable()->after('geeksforgeeks_url');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('supplementary_video_url')->nullable()->after('notes_url');
            $table->string('geeksforgeeks_url')->nullable()->after('supplementary_video_url');
            $table->string('w3schools_url')->nullable()->after('geeksforgeeks_url');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['geeksforgeeks_url', 'w3schools_url']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['supplementary_video_url', 'geeksforgeeks_url', 'w3schools_url']);
        });
    }
};
