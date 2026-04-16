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
        Schema::table('tiktok_scripts', function (Blueprint $table) {
            $table->string('audio_path')->nullable()->after('hashtags');
            $table->text('caption')->nullable()->after('audio_path');
            $table->text('onscreen_text')->nullable()->after('caption');
            $table->timestamp('kit_generated_at')->nullable()->after('onscreen_text');
        });
    }

    public function down(): void
    {
        Schema::table('tiktok_scripts', function (Blueprint $table) {
            $table->dropColumn(['audio_path', 'caption', 'onscreen_text', 'kit_generated_at']);
        });
    }
};
