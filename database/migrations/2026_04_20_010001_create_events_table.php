<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('conference');  // conference, webinar, deadline, workshop, summit
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();         // "San Francisco, CA" o "Online"
            $table->boolean('is_online')->default(false);
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('organizer')->nullable();
            $table->decimal('price', 8, 2)->nullable();    // null = gratis / varía
            $table->boolean('is_free')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
