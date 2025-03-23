<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso completo a todas las funcionalidades del sistema',
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Puede publicar y editar contenido, pero no tiene acceso a la configuración del sistema',
            ],
            [
                'name' => 'Autor',
                'slug' => 'author',
                'description' => 'Puede crear contenido pero necesita aprobación para publicarlo',
            ],
            [
                'name' => 'Usuario',
                'slug' => 'user',
                'description' => 'Puede comentar y participar en la comunidad',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
