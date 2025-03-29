<?php

namespace App\Observers;

use App\Models\News;
use App\Models\NewsHistoric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewsHistoricObserver
{
    /**
     * Handle the NewsHistoric "created" event.
     */
    public function created(NewsHistoric $newsHistoric)
    {
        // Transferir las relaciones con tags, si existen
        $tagIds = DB::table('news_tag')
            ->where('news_id', $newsHistoric->original_id)
            ->pluck('tag_id')
            ->toArray();
            
        // Si se quiere mantener una tabla específica para historicos
        if (Schema::hasTable('news_historic_tag')) {
            foreach ($tagIds as $tagId) {
                DB::table('news_historic_tag')->insert([
                    'news_historic_id' => $newsHistoric->id,
                    'tag_id' => $tagId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}