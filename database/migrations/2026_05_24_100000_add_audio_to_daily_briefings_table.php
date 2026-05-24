<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_briefings', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_briefings', 'audio_url')) {
                $table->string('audio_url', 500)->nullable()->after('generated_at');
            }
            if (!Schema::hasColumn('daily_briefings', 'audio_generated_at')) {
                $table->timestamp('audio_generated_at')->nullable()->after('audio_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_briefings', function (Blueprint $table) {
            $table->dropColumn(['audio_url', 'audio_generated_at']);
        });
    }
};
