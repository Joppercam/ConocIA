<?php

namespace App\Services;

class CommentValidationService
{
    /**
     * Lista de palabras prohibidas o inapropiadas
     * 
     * @var array
     */
    protected $bannedWords = [
        // Insultos y lenguaje ofensivo (puedes expandir esta lista según necesites)
        'idiota', 'estúpido', 'imbécil', 'pendejo', 'puta', 'marica', 'cabrón',
        'joder', 'mierda', 'pito', 'verga', 'concha', 'polla', 'gilipollas',
        
        // Palabras de spam comunes
        'viagra', 'casino', 'rolex', 'replica', 'cheap', 'free money', 'click here',
        'lottery', 'winner', 'promoción', 'promocion', 'oferta'
    ];

    /**
     * Enlaces y dominios prohibidos (sitios conocidos de spam o sitios competidores)
     * 
     * @var array
     */
    protected $bannedDomains = [
        'spam.com', 'casino.com', 'bet365', 'apuesta', 'porn', 'xxx',
        // Agrega aquí dominios de competidores si lo deseas
    ];

    /**
     * Validar el contenido de un comentario
     * 
     * @param string $content
     * @return array ['isValid' => bool, 'reason' => string|null]
     */
    public function validate(string $content): array
    {
        // Limpieza básica del texto para normalizar
        $normalizedContent = mb_strtolower(trim($content));
        
        // Verificar longitud mínima
        if (mb_strlen($normalizedContent) < 5) {
            return [
                'isValid' => false,
                'reason' => 'El comentario es demasiado corto.'
            ];
        }
        
        // Verificar longitud máxima
        if (mb_strlen($normalizedContent) > 1000) {
            return [
                'isValid' => false,
                'reason' => 'El comentario es demasiado largo.'
            ];
        }
        
        // Verificar palabras prohibidas
        foreach ($this->bannedWords as $word) {
            if (stripos($normalizedContent, $word) !== false) {
                return [
                    'isValid' => false,
                    'reason' => 'El comentario contiene lenguaje inapropiado.'
                ];
            }
        }
        
        // Verificar exceso de mayúsculas (gritar)
        $upperCount = strlen(preg_replace('/[^A-Z]/', '', $content));
        $charCount = mb_strlen($content);
        
        if ($charCount > 20 && ($upperCount / $charCount) > 0.7) {
            return [
                'isValid' => false,
                'reason' => 'El comentario usa demasiadas mayúsculas.'
            ];
        }
        
        // Verificar dominios prohibidos en URLs
        if (preg_match_all('/https?:\/\/([^\/\s]+)/', $normalizedContent, $matches)) {
            foreach ($matches[1] as $domain) {
                foreach ($this->bannedDomains as $bannedDomain) {
                    if (stripos($domain, $bannedDomain) !== false) {
                        return [
                            'isValid' => false,
                            'reason' => 'El comentario contiene enlaces a sitios prohibidos.'
                        ];
                    }
                }
            }
        }
        
        // Verificar caracteres repetidos (posible spam)
        if (preg_match('/(.)\1{5,}/', $normalizedContent)) {
            return [
                'isValid' => false,
                'reason' => 'El comentario contiene caracteres repetitivos.'
            ];
        }

        // Verificar demasiados signos de exclamación o interrogación (spam)
        if (substr_count($normalizedContent, '!') > 5 || substr_count($normalizedContent, '?') > 5) {
            return [
                'isValid' => false,
                'reason' => 'El comentario contiene demasiados signos de exclamación o interrogación.'
            ];
        }
        
        // Verificar demasiados enlaces (posible spam)
        if (substr_count($normalizedContent, 'http') > 2) {
            return [
                'isValid' => false,
                'reason' => 'El comentario contiene demasiados enlaces.'
            ];
        }

        // Si pasa todas las validaciones, el comentario es válido
        return [
            'isValid' => true,
            'reason' => null
        ];
    }
    
    /**
     * Agregar palabras prohibidas personalizadas
     * 
     * @param array $words
     * @return void
     */
    public function addBannedWords(array $words)
    {
        $this->bannedWords = array_merge($this->bannedWords, $words);
    }
    
    /**
     * Agregar dominios prohibidos personalizados
     * 
     * @param array $domains
     * @return void
     */
    public function addBannedDomains(array $domains)
    {
        $this->bannedDomains = array_merge($this->bannedDomains, $domains);
    }
}