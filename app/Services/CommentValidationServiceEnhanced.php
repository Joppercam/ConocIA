<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class CommentValidationServiceEnhanced extends CommentValidationService
{
    /**
     * Servicio de análisis de texto
     * 
     * @var TextAnalysisService
     */
    protected $textAnalysis;
    
    /**
     * Habilitar/deshabilitar análisis avanzado
     * 
     * @var bool
     */
    protected $enableAdvancedAnalysis;

    /**
     * Constructor
     * 
     * @param TextAnalysisService $textAnalysis
     */
    public function __construct(TextAnalysisService $textAnalysis)
    {
        parent::__construct();
        
        $this->textAnalysis = $textAnalysis;
        $this->enableAdvancedAnalysis = Config::get('comments.enable_advanced_analysis', false);
    }
    
    /**
     * Validar el contenido de un comentario con análisis avanzado
     * 
     * @param string $content
     * @return array ['isValid' => bool, 'reason' => string|null]
     */
    public function validate(string $content): array
    {
        // Realizar validación básica primero
        $basicValidation = parent::validate($content);
        
        // Si la validación básica falla, no es necesario continuar
        if (!$basicValidation['isValid']) {
            return $basicValidation;
        }
        
        // Si el análisis avanzado está deshabilitado, devolver la validación básica
        if (!$this->enableAdvancedAnalysis) {
            return $basicValidation;
        }
        
        // Análisis avanzado de toxicidad si está habilitado
        $toxicityResult = $this->textAnalysis->detectToxicity($content);
        
        if ($toxicityResult['success'] && $toxicityResult['is_toxic']) {
            return [
                'isValid' => false,
                'reason' => 'Contenido inapropiado detectado: ' . $toxicityResult['reason'],
                'score' => $toxicityResult['score']
            ];
        }
        
        // Análisis de spam
        if ($this->textAnalysis->isSpam($content)) {
            return [
                'isValid' => false,
                'reason' => 'El comentario parece ser spam.'
            ];
        }
        
        // Si todo está bien, el comentario es válido
        return [
            'isValid' => true,
            'reason' => null
        ];
    }
}