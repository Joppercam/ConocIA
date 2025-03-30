<?php
// app/Observers/NewsObserver.php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Str;

class NewsObserver
{
    /**
     * Handle the News "creating" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function creating(News $news)
    {
        $this->generateSlug($news);
    }

    /**
     * Handle the News "updating" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function updating(News $news)
    {
        // Solo regenerar el slug si el título ha cambiado
        if ($news->isDirty('title') && !$news->isDirty('slug')) {
            $this->generateSlug($news);
        }
    }

    /**
     * Genera un slug único basado en el título
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    protected function generateSlug(News $news)
    {
        if (empty($news->slug)) {
            $slug = Str::slug($news->title);
            
            // Verificar si el slug ya existe
            $count = 0;
            $originalSlug = $slug;
            
            while (News::where('slug', $slug)
                     ->where('id', '!=', $news->id)
                     ->exists()) {
                $count++;
                $slug = $originalSlug . '-' . $count;
            }
            
            $news->slug = $slug;
        }
    }
}