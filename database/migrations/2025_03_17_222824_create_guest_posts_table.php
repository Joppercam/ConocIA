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
        Schema::create('guest_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Usuario que aprueba/gestiona
            $table->enum('status', ['pending', 'approved', 'rejected', 'published'])->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->text('author_bio')->nullable();
            $table->string('author_website')->nullable();
            $table->string('author_twitter')->nullable();
            $table->string('author_linkedin')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_posts');
    }
};
