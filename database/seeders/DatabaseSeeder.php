<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden lógico
        $this->call([
            RoleSeeder::class,      // Primero roles
            CategorySeeder::class,  // Luego categorías
            UserSeeder::class,      // Finalmente usuarios (que dependen de roles)
            NewsSeeder::class,
            ResearchSeeder::class,
            VideoPlatformSeeder::class,
            VideoCategorySeeder::class,
        ]);
    }
}
