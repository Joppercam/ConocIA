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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 🔥 La columna "name" debe existir aquí
            $table->string('email')->unique();
            $table->string('password');
            $table->string('username')->unique()->nullable();
            $table->string('profile_photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('website')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable(); // 🔥 Agrega esta línea
            $table->rememberToken();
            $table->timestamps();

            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn([
                'username',
                'profile_photo',
                'bio',
                'website',
                'twitter',
                'linkedin',
                'github',
                'is_active'
            ]);
        });
    }
};

