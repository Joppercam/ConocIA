<?php
// app/Utilities/OpenGraph.php

namespace App\Utilities;

class OpenGraph
{
    /**
     * Genera los metadatos OpenGraph para noticias
     *
     * @param  \App\Models\News  $news
     * @return array
     */
    public static function forNews($news)
    {
        $authorName = is_object($news->author) ? $news->author->name : ($news->author ?? 'ConocIA');
        $imageUrl = !empty($news->image) && !str_contains($news->image, 'default') 
            ? getImageUrl($news->image, 'news', 'large') 
            : asset('storage/images/defaults/social-share.jpg');
        $categoryName = is_object($news->category) ? $news->category->name : ($news->category ?? 'Tecnología');
        
        $description = $news->summary ?? $news->excerpt ?? substr(strip_tags($news->content), 0, 160);
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return [
            'title' => $news->title,
            'description' => $description,
            'type' => 'article',
            'url' => route('news.show', $news->slug ?? $news->id),
            'image' => $imageUrl,
            'published_time' => $news->published_at ? $news->published_at->toIso8601String() : $news->created_at->toIso8601String(),
            'modified_time' => $news->updated_at ? $news->updated_at->toIso8601String() : null,
            'author' => $authorName,
            'section' => $categoryName,
            'tags' => collect($news->tags ?? [])->pluck('name')->implode(', '),
            'site_name' => config('app.name', 'ConocIA')
        ];
    }

    /**
     * Genera los metadatos OpenGraph para investigaciones
     *
     * @param  \App\Models\Research  $research
     * @return array
     */
    public static function forResearch($research)
    {
        $authorName = is_object($research->author) ? $research->author->name : ($research->author ?? 'ConocIA');
        $imageUrl = !empty($research->image) && !str_contains($research->image, 'default') 
            ? getImageUrl($research->image, 'research', 'large') 
            : asset('storage/images/defaults/research-social-share.jpg');
        
        $description = $research->summary ?? $research->excerpt ?? substr(strip_tags($research->content), 0, 160);
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return [
            'title' => $research->title,
            'description' => $description,
            'type' => 'article',
            'url' => route('research.show', $research->slug ?? $research->id),
            'image' => $imageUrl,
            'published_time' => $research->created_at->toIso8601String(),
            'modified_time' => $research->updated_at->toIso8601String(),
            'author' => $authorName,
            'site_name' => config('app.name', 'ConocIA')
        ];
    }

    /**
     * Genera los metadatos OpenGraph para columnas de opinión
     *
     * @param  \App\Models\Column  $column
     * @return array
     */
    public static function forColumn($column)
    {
        $authorName = is_object($column->author) ? $column->author->name : ($column->author ?? 'Columnista');
        $imageUrl = is_object($column->author) && !empty($column->author->avatar) 
            ? getImageUrl($column->author->avatar, 'avatars', 'large') 
            : asset('storage/images/defaults/columnist-social-share.jpg');
        
        $description = $column->excerpt ?? substr(strip_tags($column->content), 0, 160);
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return [
            'title' => $column->title,
            'description' => $description,
            'type' => 'article',
            'url' => route('columns.show', $column->slug ?? $column->id),
            'image' => $imageUrl,
            'published_time' => $column->created_at->toIso8601String(),
            'modified_time' => $column->updated_at->toIso8601String(),
            'author' => $authorName,
            'site_name' => config('app.name', 'ConocIA')
        ];
    }
}