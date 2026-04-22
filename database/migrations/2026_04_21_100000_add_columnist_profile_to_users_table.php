<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('academic_title')->nullable()->after('bio');     // "Dr.", "Mg.", "Investigador"
            $table->string('institution')->nullable()->after('academic_title');  // "U. de Chile", "CMM"
            $table->string('expertise_area')->nullable()->after('institution');  // "NLP", "Ética IA"
            $table->string('linkedin_url')->nullable()->after('expertise_area');
            $table->boolean('is_featured_columnist')->default(false)->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'academic_title', 'institution', 'expertise_area',
                'linkedin_url', 'is_featured_columnist',
            ]);
        });
    }
};
