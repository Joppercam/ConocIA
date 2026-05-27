<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('columns')->where('slug', 'la-ia-que-inventa-leyes-el-peligro-silencioso-que-llega-a-los-tribunales')->exists()) {
            return;
        }

        Artisan::call('content:publish-legal-hallucination-column');
    }

    public function down(): void
    {
        DB::table('columns')
            ->where('slug', 'la-ia-que-inventa-leyes-el-peligro-silencioso-que-llega-a-los-tribunales')
            ->delete();
    }
};
