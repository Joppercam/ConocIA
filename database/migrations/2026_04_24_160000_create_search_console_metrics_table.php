<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_console_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('site_url');
            $table->date('metric_date');
            $table->string('search_type', 32)->default('web');
            $table->string('dimension_type', 32);
            $table->text('page')->nullable();
            $table->text('query')->nullable();
            $table->text('country')->nullable();
            $table->string('device', 32)->nullable();
            $table->string('dimension_key_hash', 64);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->decimal('ctr', 8, 5)->default(0);
            $table->decimal('position', 8, 3)->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['site_url', 'metric_date']);
            $table->index(['dimension_type', 'metric_date']);
            $table->index(['search_type', 'metric_date']);
            $table->unique(
                ['site_url', 'metric_date', 'search_type', 'dimension_type', 'dimension_key_hash'],
                'search_console_metrics_unique_row'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_console_metrics');
    }
};
