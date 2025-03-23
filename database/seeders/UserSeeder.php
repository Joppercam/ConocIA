<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRole = Role::where('slug', 'admin')->first();
        $editorRole = Role::where('slug', 'editor')->first();
        $authorRole = Role::where('slug', 'author')->first();
        $userRole = Role::where('slug', 'user')->first();
        
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@conocia.com',
            'password' => Hash::make('password'),
            'username' => 'admin',
            'email_verified_at' => now(),
            'role_id' => $adminRole->id,
            'bio' => 'Administrador del sitio ConocIA',
            'is_active' => true,
        ]);
        
        // Crear usuario editor
        User::create([
            'name' => 'Editor',
            'email' => 'editor@conocia.com',
            'password' => Hash::make('password'),
            'username' => 'editor',
            'email_verified_at' => now(),
            'role_id' => $editorRole->id,
            'bio' => 'Editor principal de ConocIA',
            'is_active' => true,
        ]);
        
        // Crear autor
        User::create([
            'name' => 'Autor',
            'email' => 'autor@conocia.com',
            'password' => Hash::make('password'),
            'username' => 'autor',
            'email_verified_at' => now(),
            'role_id' => $authorRole->id,
            'bio' => 'Autor de contenido especializado en IA',
            'is_active' => true,
        ]);
        
        // Crear usuario normal
        User::create([
            'name' => 'Usuario',
            'email' => 'usuario@conocia.com',
            'password' => Hash::make('password'),
            'username' => 'usuario',
            'email_verified_at' => now(),
            'role_id' => $userRole->id,
            'bio' => 'Usuario de ConocIA interesado en inteligencia artificial',
            'is_active' => true,
        ]);
    }
}
