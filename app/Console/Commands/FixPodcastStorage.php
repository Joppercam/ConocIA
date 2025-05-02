<?php
// Archivo: app/Console/Commands/FixPodcastStorage.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FixPodcastStorage extends Command
{
    protected $signature = 'podcasts:fix-storage';
    protected $description = 'Repara problemas de almacenamiento para podcasts';

    public function handle()
    {
        $this->info('Verificando y solucionando problemas de almacenamiento para podcasts...');

        // 1. Verificar si existe el enlace simbólico
        $publicPath = public_path('storage');
        if (!file_exists($publicPath)) {
            $this->warn('El enlace simbólico de storage no existe. Creando...');
            $this->call('storage:link');
        } else {
            $this->info('Enlace simbólico OK ✓');
        }

        // 2. Verificar y crear directorios necesarios
        $directories = [
            'podcasts',
            'podcasts/test',
        ];

        foreach ($directories as $dir) {
            $storagePath = storage_path('app/public/' . $dir);
            if (!file_exists($storagePath)) {
                $this->warn("Directorio no encontrado: $dir. Creando...");
                
                if (!File::makeDirectory($storagePath, 0775, true)) {
                    $this->error("No se pudo crear el directorio: $storagePath");
                    $this->warn("Intentando resolver manualmente...");
                    
                    // Intentar crearlo manualmente
                    if (!mkdir($storagePath, 0775, true)) {
                        $this->error("Error crítico: No se puede crear el directorio $storagePath");
                        $this->info("Por favor, cree manualmente este directorio y establezca los permisos correctos.");
                    } else {
                        $this->info("Directorio creado manualmente: $storagePath ✓");
                    }
                } else {
                    $this->info("Directorio creado: $dir ✓");
                }
            } else {
                $this->info("Directorio $dir OK ✓");
            }
        }

        // 3. Verificar permisos
        foreach ($directories as $dir) {
            $storagePath = storage_path('app/public/' . $dir);
            if (file_exists($storagePath)) {
                $perms = substr(sprintf('%o', fileperms($storagePath)), -4);
                $this->info("Permisos para $dir: $perms");
                
                if (!is_writable($storagePath)) {
                    $this->error("El directorio $storagePath no tiene permisos de escritura.");
                    $this->info("Intentando establecer permisos...");
                    
                    if (!chmod($storagePath, 0775)) {
                        $this->error("No se pudieron cambiar los permisos. Por favor, ejecute manualmente:");
                        $this->line("chmod -R 775 " . $storagePath);
                    } else {
                        $this->info("Permisos actualizados ✓");
                    }
                }
            }
        }

        // 4. Verificar si podemos escribir un archivo de prueba
        $testFile = 'podcasts/test/storage_test_' . time() . '.txt';
        try {
            Storage::put('public/' . $testFile, 'Test de escritura para podcasts');
            $this->info("Prueba de escritura exitosa: $testFile ✓");
            
            // Verificar si el archivo existe físicamente
            $fullPath = storage_path('app/public/' . $testFile);
            if (file_exists($fullPath)) {
                $this->info("Archivo físico creado correctamente ✓");
                
                // Verificar si es accesible a través de la URL
                $url = asset('storage/' . $testFile);
                $this->info("URL del archivo de prueba: $url");
                $this->info("Intente acceder a esta URL para verificar la configuración.");
            } else {
                $this->error("El archivo no se creó físicamente a pesar de que Storage::put devolvió éxito.");
                $this->error("Hay un problema en la configuración del sistema de archivos.");
            }
        } catch (\Exception $e) {
            $this->error("Error al escribir archivo de prueba: " . $e->getMessage());
        }

        // 5. Información de diagnóstico
        $this->info("\nInformación de diagnóstico:");
        $this->line("- Ruta de almacenamiento: " . storage_path('app/public'));
        $this->line("- Ruta pública: " . public_path());
        $this->line("- Usuario PHP: " . exec('whoami'));
        $this->line("- Disco de almacenamiento: " . config('filesystems.default'));
        $this->line("- Driver público: " . config('filesystems.disks.public.driver'));
        
        $this->info("\nSi los problemas persisten, considere estas soluciones:");
        $this->line("1. Ejecute 'php artisan storage:link' para recrear el enlace simbólico.");
        $this->line("2. Asegúrese de que el usuario web tenga permisos en: " . storage_path('app/public'));
        $this->line("3. Si está en producción, verifique la configuración del servidor web.");
        $this->line("4. Verifique la configuración de filesystems.php para asegurarse de que está utilizando el disco correcto.");
    }
}