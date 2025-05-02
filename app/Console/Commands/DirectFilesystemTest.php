<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DirectFilesystemTest extends Command
{
    protected $signature = 'podcasts:direct-test';
    protected $description = 'Pruebas directas del sistema de archivos en Windows';

    public function handle()
    {
        $this->info('Iniciando pruebas directas de sistema de archivos en Windows');
        
        // 1. Imprimir información de rutas con formato consistente
        $storagePath = str_replace('/', '\\', storage_path('app\\public'));
        $this->info("Ruta de almacenamiento (formato Windows): {$storagePath}");
        
        // 2. Prueba con Storage Facade
        $testContent = "Prueba de escritura con Storage: " . date('Y-m-d H:i:s');
        $testFile = 'direct_test_' . time() . '.txt';
        
        $this->info("Intentando guardar archivo con Storage Facade: {$testFile}");
        try {
            $result = Storage::disk('public')->put($testFile, $testContent);
            $this->info("Resultado de Storage::put: " . ($result ? 'Éxito' : 'Fallo'));
            
            // Verificar si existe el archivo
            $fullPath = $storagePath . '\\' . $testFile;
            $this->info("Verificando archivo en: {$fullPath}");
            if (file_exists($fullPath)) {
                $this->info("✓ El archivo existe físicamente");
                $this->info("Contenido: " . file_get_contents($fullPath));
            } else {
                $this->error("× El archivo NO existe físicamente a pesar del éxito reportado");
            }
        } catch (\Exception $e) {
            $this->error("Error con Storage: " . $e->getMessage());
        }
        
        // 3. Prueba directa con file_put_contents
        $directTestFile = 'direct_write_' . time() . '.txt';
        $directFullPath = $storagePath . '\\' . $directTestFile;
        $directContent = "Prueba de escritura directa: " . date('Y-m-d H:i:s');
        
        $this->info("\nIntentando escribir directamente con file_put_contents");
        $this->info("Ruta: {$directFullPath}");
        
        try {
            $bytes = file_put_contents($directFullPath, $directContent);
            $this->info("Resultado: " . ($bytes !== false ? "{$bytes} bytes escritos" : "Fallo"));
            
            if (file_exists($directFullPath)) {
                $this->info("✓ El archivo existe físicamente");
                $this->info("Contenido: " . file_get_contents($directFullPath));
            } else {
                $this->error("× El archivo NO existe físicamente");
            }
        } catch (\Exception $e) {
            $this->error("Error con file_put_contents: " . $e->getMessage());
        }
        
        // 4. Prueba con rutas absolutas y creación de directorio
        $testDir = $storagePath . '\\podcasts\\test_direct';
        $this->info("\nPrueba con directorio específico: {$testDir}");
        
        if (!file_exists($testDir)) {
            $this->info("Creando directorio...");
            if (mkdir($testDir, 0777, true)) {
                $this->info("✓ Directorio creado exitosamente");
            } else {
                $this->error("× No se pudo crear el directorio");
                return 1;
            }
        } else {
            $this->info("El directorio ya existe");
        }
        
        $specificFile = $testDir . '\\specific_' . time() . '.txt';
        $specificContent = "Prueba específica de directorio: " . date('Y-m-d H:i:s');
        
        $this->info("Escribiendo archivo en directorio específico: {$specificFile}");
        try {
            $bytes = file_put_contents($specificFile, $specificContent);
            $this->info("Resultado: " . ($bytes !== false ? "{$bytes} bytes escritos" : "Fallo"));
            
            if (file_exists($specificFile)) {
                $this->info("✓ El archivo existe físicamente");
                $this->info("Contenido: " . file_get_contents($specificFile));
                
                // Calcular ruta relativa para URL
                $relativePath = str_replace($storagePath . '\\', '', $specificFile);
                $relativePath = str_replace('\\', '/', $relativePath);
                $this->info("URL accesible: " . asset('storage/' . $relativePath));
            } else {
                $this->error("× El archivo NO existe físicamente");
            }
        } catch (\Exception $e) {
            $this->error("Error con file_put_contents en directorio específico: " . $e->getMessage());
        }
        
        // 5. Verificar la configuración de filesystems.php
        $this->info("\nVerificando configuración de filesystems.php:");
        $this->info("Disco local root: " . config('filesystems.disks.local.root'));
        $this->info("Disco public root: " . config('filesystems.disks.public.root'));
        $this->info("Disco public url: " . config('filesystems.disks.public.url'));
        
        return 0;
    }
}