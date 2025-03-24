<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateDefaultImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:create-defaults-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea imágenes predeterminadas simples para categorías';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Crear directorio
        Storage::disk('public')->makeDirectory('images/defaults', 0755, true);
        
        // Categorías y colores
        $categories = [
            'ai' => ['Inteligencia Artificial', '#3498db'],
            'technology' => ['Tecnología', '#2ecc71'],
            'research' => ['Investigación', '#9b59b6'],
            'robotics' => ['Robótica', '#e74c3c'],
            'cybersecurity' => ['Ciberseguridad', '#f39c12'],
            'innovation' => ['Innovación', '#1abc9c'],
            'ethics' => ['Ética', '#8e44ad'],
            'generic-tech' => ['Noticias Tech', '#34495e'],
        ];
        
        foreach ($categories as $slug => [$name, $color]) {
            $this->createSimpleImage($slug, $name, $color);
        }
        
        $this->info('Imágenes predeterminadas creadas correctamente.');
    }
    
    /**
     * Crea una imagen SVG simple
     */
    private function createSimpleImage($slug, $name, $background)
    {
        $this->info("Creando imagen SVG para $name...");
        
        // Crear un SVG simple
        $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
        $svg .= '<svg width="1200" height="630" xmlns="http://www.w3.org/2000/svg">';
        
        // Fondo
        $svg .= '<rect width="1200" height="630" fill="' . $background . '" />';
        
        // Título
        $svg .= '<text x="600" y="315" font-family="Arial, sans-serif" font-size="60" text-anchor="middle" fill="white">' . $name . '</text>';
        
        $svg .= '</svg>';
        
        // Guardar el SVG
        Storage::disk('public')->put("images/defaults/{$slug}.svg", $svg);
        
        $this->info("Imagen SVG para {$name} creada: images/defaults/{$slug}.svg");
    }
}