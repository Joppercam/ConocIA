<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('columns')->where('slug', 'ia-y-desigualdad-en-chile-la-nueva-brecha-que-nadie-esta-midiendo')->exists()) {
            return;
        }

        Artisan::call('content:publish-ai-inequality-chile-column');
    }

    public function down(): void
    {
        DB::table('columns')
            ->where('slug', 'ia-y-desigualdad-en-chile-la-nueva-brecha-que-nadie-esta-midiendo')
            ->delete();
    }
};
