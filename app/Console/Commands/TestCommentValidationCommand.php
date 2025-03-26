<?php

namespace App\Console\Commands;

use App\Services\CommentValidationService;
use Illuminate\Console\Command;

class TestCommentValidationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:test-validation {text?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test comment validation with sample text';

    /**
     * Execute the console command.
     *
     * @param CommentValidationService $validator
     * @return int
     */
    public function handle(CommentValidationService $validator)
    {
        $text = $this->argument('text');
        
        if (!$text) {
            $text = $this->ask('Ingrese el texto del comentario para validar:');
        }
        
        $this->info('Validando el siguiente texto:');
        $this->line($text);
        $this->newLine();
        
        $this->info('Resultados de la validación:');
        $result = $validator->validate($text);
        
        if ($result['isValid']) {
            $this->info('✓ El comentario es válido y sería aprobado automáticamente.');
        } else {
            $this->error('✗ El comentario NO es válido y requeriría revisión manual.');
            $this->line('Razón: ' . $result['reason']);
            
            if ($validator->shouldAutoReject()) {
                $this->warn('El comentario sería rechazado automáticamente según la configuración actual.');
            } else {
                $this->warn('El comentario quedaría pendiente para revisión manual según la configuración actual.');
            }
        }
        
        $this->newLine();
        $this->info('Configuración activa:');
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['Longitud mínima', config('comments.min_length', 5)],
                ['Longitud máxima', config('comments.max_length', 1000)],
                ['Máximo ratio de mayúsculas', config('comments.max_uppercase_ratio', 0.7)],
                ['Máximo de enlaces', config('comments.max_links', 2)],
                ['Máximo de puntuación', config('comments.max_punctuation', 5)],
                ['Rechazo automático', config('comments.auto_reject', false) ? 'Sí' : 'No'],
                ['Análisis avanzado', config('comments.enable_advanced_analysis', false) ? 'Sí' : 'No'],
            ]
        );
        
        return 0;
    }
}