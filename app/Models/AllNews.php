<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllNews extends Model
{
    protected $table = 'all_news_view';
    
    // La vista es de solo lectura
    public $incrementing = false;
    public $timestamps = false;
    
    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    
    // Método para obtener el modelo original
    public function getOriginalModel()
    {
        if ($this->source_table === 'active') {
            return News::find($this->id);
        } else {
            return NewsHistoric::where('original_id', $this->id)->first();
        }
    }
}