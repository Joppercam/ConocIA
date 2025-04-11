<?php

// database/seeders/ClaimCategorySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClaimCategory;
use Illuminate\Support\Str;

class ClaimCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Política',
                'description' => 'Afirmaciones relacionadas con política, gobierno y figuras políticas',
            ],
            [
                'name' => 'Economía',
                'description' => 'Afirmaciones sobre economía, finanzas, impuestos y temas relacionados',
            ],
            [
                'name' => 'Salud',
                'description' => 'Afirmaciones sobre salud, medicina, enfermedades y tratamientos',
            ],
            [
                'name' => 'Ciencia',
                'description' => 'Afirmaciones relacionadas con ciencia, investigación y descubrimientos',
            ],
            [
                'name' => 'Medio Ambiente',
                'description' => 'Afirmaciones sobre clima, contaminación y temas ambientales',
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Afirmaciones sobre tecnología, internet y temas digitales',
            ],
            [
                'name' => 'Educación',
                'description' => 'Afirmaciones relacionadas con educación, escuelas y aprendizaje',
            ],
            [
                'name' => 'Sociedad',
                'description' => 'Afirmaciones sobre temas sociales, culturales y comunitarios',
            ],
            [
                'name' => 'Internacional',
                'description' => 'Afirmaciones sobre asuntos internacionales y relaciones entre países',
            ],
            [
                'name' => 'Deportes',
                'description' => 'Afirmaciones relacionadas con deportes y eventos deportivos',
            ],
        ];
        
        foreach ($categories as $category) {
            ClaimCategory::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'slug' => Str::slug($category['name']),
            ]);
        }
    }
}