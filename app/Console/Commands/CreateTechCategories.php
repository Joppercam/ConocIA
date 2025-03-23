<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTechCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:create-tech';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea las categorías relacionadas con tecnología para el portal de noticias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Definir las categorías de tecnología
        $categories = [
            'Inteligencia Artificial' => 'Noticias sobre IA, machine learning, deep learning y sistemas inteligentes',
            'Tecnología' => 'Noticias generales sobre tecnología, informática y avances digitales',
            'Investigación' => 'Noticias sobre investigación tecnológica, estudios científicos y nuevos descubrimientos',
            'Robótica' => 'Noticias sobre robots, automatización, drones y sistemas autónomos',
            'Ciberseguridad' => 'Noticias sobre seguridad informática, hacking, amenazas y protección digital',
            'Innovación' => 'Noticias sobre startups tecnológicas, innovaciones disruptivas y nuevos modelos',
            'Ética' => 'Noticias sobre aspectos éticos de la tecnología, privacidad y regulación',
        ];

        $count = 0;

        // Insertar las categorías en la base de datos
        foreach ($categories as $name => $description) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $description,
                ]
            );

            if ($category->wasRecentlyCreated) {
                $this->info("Categoría '{$name}' creada correctamente.");
                $count++;
            } else {
                $this->info("La categoría '{$name}' ya existe.");
            }
        }

        $this->info("{$count} categorías nuevas creadas.");
    }
}