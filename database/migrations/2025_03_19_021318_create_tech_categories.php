<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTechCategories extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
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

        // Insertar las categorías en la base de datos
        foreach ($categories as $name => $description) {
            DB::table('categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar las categorías creadas
        $categoryNames = [
            'Inteligencia Artificial',
            'Tecnología',
            'Investigación',
            'Robótica',
            'Ciberseguridad',
            'Innovación',
            'Ética',
        ];

        DB::table('categories')->whereIn('name', $categoryNames)->delete();
    }
}