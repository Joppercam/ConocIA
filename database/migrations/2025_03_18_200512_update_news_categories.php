<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\News;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Obtiene todas las noticias que tienen category como string pero no category_id
        $news = News::whereNotNull('category')
                    ->whereNull('category_id')
                    ->get();
        
        foreach ($news as $article) {
            // Busca o crea la categoría
            $category = Category::firstOrCreate(
                ['name' => $article->category],
                ['slug' => \Illuminate\Support\Str::slug($article->category)]
            );
            
            // Asigna el category_id
            $article->category_id = $category->id;
            $article->save();
        }
        
        // Opcionalmente, elimina la columna category si ya no la necesitas
        if (Schema::hasColumn('news', 'category')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            //
        });
    }
};
