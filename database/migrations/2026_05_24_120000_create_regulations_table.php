<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regulations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('scope', ['chile', 'internacional']);
            $table->enum('status', ['en_tramitacion', 'aprobada', 'vigente', 'rechazada', 'propuesta']);
            $table->text('summary');
            $table->text('content')->nullable();
            $table->string('source_url')->nullable();
            $table->string('institution');
            $table->date('date_introduced')->nullable();
            $table->date('date_updated')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regulations');
    }
};
