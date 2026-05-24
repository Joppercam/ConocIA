<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecosystem_actors', function (Blueprint $table) {
            if (!Schema::hasColumn('ecosystem_actors', 'key_facts')) {
                $table->json('key_facts')->nullable()->after('focus_areas');
            }
            if (!Schema::hasColumn('ecosystem_actors', 'director')) {
                $table->string('director')->nullable()->after('key_facts');
            }
            if (!Schema::hasColumn('ecosystem_actors', 'founded')) {
                $table->string('founded')->nullable()->after('director');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ecosystem_actors', function (Blueprint $table) {
            $table->dropColumn(['key_facts', 'director', 'founded']);
        });
    }
};
