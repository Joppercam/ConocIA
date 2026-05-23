<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radar_regulatorio', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->enum('tipo', ['proyecto_ley', 'decreto', 'politica', 'anuncio', 'informe', 'consulta'])->default('anuncio');
            $table->enum('estado', ['en_tramite', 'aprobado', 'rechazado', 'promulgado', 'vigente', 'en_consulta', 'archivado'])->default('en_tramite');
            $table->string('organismo')->nullable();
            $table->date('fecha_evento')->nullable();
            $table->enum('relevancia', ['alta', 'media', 'baja'])->default('media');
            $table->string('fuente_url', 500)->nullable();
            $table->json('key_actors')->nullable();
            $table->integer('reading_time')->default(3);
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radar_regulatorio');
    }
};
