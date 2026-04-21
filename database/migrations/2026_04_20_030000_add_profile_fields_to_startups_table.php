<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->longText('profile_content')->nullable()->after('description');
            $table->string('key_quote', 400)->nullable()->after('profile_content');
            $table->text('why_it_matters')->nullable()->after('key_quote');
            $table->json('founder_names')->nullable()->after('why_it_matters');
            $table->date('featured_week')->nullable()->after('founder_names')->index();
        });
    }

    public function down(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->dropColumn(['profile_content', 'key_quote', 'why_it_matters', 'founder_names', 'featured_week']);
        });
    }
};
