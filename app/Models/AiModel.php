<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    protected $table = 'ai_models';

    protected $fillable = [
        'name', 'slug', 'company', 'company_slug', 'logo', 'type', 'access', 'release_date',
        'cap_text', 'cap_image_input', 'cap_image_output', 'cap_code', 'cap_voice',
        'cap_web_search', 'cap_files', 'cap_reasoning',
        'context_window', 'parameters', 'context_window_label',
        'price_input', 'price_output', 'has_free_tier',
        'score_mmlu', 'score_humaneval', 'score_math',
        'description', 'official_url', 'featured', 'active', 'sort_order',
    ];

    protected $casts = [
        'cap_text' => 'boolean', 'cap_image_input' => 'boolean', 'cap_image_output' => 'boolean',
        'cap_code' => 'boolean', 'cap_voice' => 'boolean', 'cap_web_search' => 'boolean',
        'cap_files' => 'boolean', 'cap_reasoning' => 'boolean',
        'has_free_tier' => 'boolean', 'featured' => 'boolean', 'active' => 'boolean',
        'price_input' => 'decimal:4', 'price_output' => 'decimal:4',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getPriceLabel(): string
    {
        if (!$this->price_input && !$this->price_output) {
            return $this->has_free_tier ? 'Gratis' : 'Varía';
        }
        return '$' . number_format($this->price_input, 2) . ' / $' . number_format($this->price_output, 2);
    }

    public function getCapabilities(): array
    {
        $caps = [];
        if ($this->cap_text)         $caps[] = ['key' => 'text',         'label' => 'Texto',         'icon' => 'fa-font'];
        if ($this->cap_image_input)  $caps[] = ['key' => 'image_input',  'label' => 'Ver imágenes',  'icon' => 'fa-image'];
        if ($this->cap_image_output) $caps[] = ['key' => 'image_output', 'label' => 'Generar imágenes','icon' => 'fa-paint-brush'];
        if ($this->cap_code)         $caps[] = ['key' => 'code',         'label' => 'Código',         'icon' => 'fa-code'];
        if ($this->cap_voice)        $caps[] = ['key' => 'voice',        'label' => 'Voz',            'icon' => 'fa-microphone'];
        if ($this->cap_web_search)   $caps[] = ['key' => 'web_search',   'label' => 'Búsqueda web',  'icon' => 'fa-search'];
        if ($this->cap_files)        $caps[] = ['key' => 'files',        'label' => 'Archivos',       'icon' => 'fa-file'];
        if ($this->cap_reasoning)    $caps[] = ['key' => 'reasoning',    'label' => 'Razonamiento',   'icon' => 'fa-brain'];
        return $caps;
    }
}
