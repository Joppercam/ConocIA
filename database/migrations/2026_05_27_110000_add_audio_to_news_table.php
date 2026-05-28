<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('audio_path')->nullable()->after('reading_time');
            $table->timestamp('audio_generated_at')->nullable()->after('audio_path');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['audio_path', 'audio_generated_at']);
        });
    }
};
