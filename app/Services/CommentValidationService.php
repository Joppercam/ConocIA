<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class CommentValidationService
{
    protected array $bannedWords = [
        'idiota', 'estúpido', 'imbécil', 'pendejo', 'puta', 'marica', 'cabrón',
        'joder', 'mierda', 'pito', 'verga', 'concha', 'polla', 'gilipollas',
        'viagra', 'casino', 'rolex', 'replica', 'cheap', 'free money', 'click here',
        'lottery', 'winner', 'promoción', 'promocion', 'oferta',
    ];

    protected array $bannedDomains = [
        'spam.com', 'casino.com', 'bet365', 'apuesta', 'porn', 'xxx',
    ];

    protected ?TextAnalysisService $textAnalysis;
    protected bool $enableAdvancedAnalysis;

    public function __construct(?TextAnalysisService $textAnalysis = null)
    {
        $this->textAnalysis           = $textAnalysis;
        $this->enableAdvancedAnalysis = Config::get('comments.enable_advanced_analysis', false);
    }

    /**
     * Valida el contenido de un comentario.
     * Si `comments.enable_advanced_analysis` está activo y hay un TextAnalysisService
     * disponible, también aplica detección de toxicidad y spam.
     *
     * @return array{isValid: bool, reason: string|null}
     */
    public function validate(string $content): array
    {
        $normalized = mb_strtolower(trim($content));

        if (mb_strlen($normalized) < 5) {
            return ['isValid' => false, 'reason' => 'El comentario es demasiado corto.'];
        }

        if (mb_strlen($normalized) > 1000) {
            return ['isValid' => false, 'reason' => 'El comentario es demasiado largo.'];
        }

        foreach ($this->bannedWords as $word) {
            if (stripos($normalized, $word) !== false) {
                return ['isValid' => false, 'reason' => 'El comentario contiene lenguaje inapropiado.'];
            }
        }

        $upperCount = strlen(preg_replace('/[^A-Z]/', '', $content));
        $charCount  = mb_strlen($content);
        if ($charCount > 20 && ($upperCount / $charCount) > 0.7) {
            return ['isValid' => false, 'reason' => 'El comentario usa demasiadas mayúsculas.'];
        }

        if (preg_match_all('/https?:\/\/([^\/\s]+)/', $normalized, $matches)) {
            foreach ($matches[1] as $domain) {
                foreach ($this->bannedDomains as $banned) {
                    if (stripos($domain, $banned) !== false) {
                        return ['isValid' => false, 'reason' => 'El comentario contiene enlaces a sitios prohibidos.'];
                    }
                }
            }
        }

        if (preg_match('/(.)\1{5,}/', $normalized)) {
            return ['isValid' => false, 'reason' => 'El comentario contiene caracteres repetitivos.'];
        }

        if (substr_count($normalized, '!') > 5 || substr_count($normalized, '?') > 5) {
            return ['isValid' => false, 'reason' => 'El comentario contiene demasiados signos de exclamación o interrogación.'];
        }

        if (substr_count($normalized, 'http') > 2) {
            return ['isValid' => false, 'reason' => 'El comentario contiene demasiados enlaces.'];
        }

        // Análisis avanzado (opcional)
        if ($this->enableAdvancedAnalysis && $this->textAnalysis !== null) {
            $toxicity = $this->textAnalysis->detectToxicity($content);
            if ($toxicity['success'] && $toxicity['is_toxic']) {
                return ['isValid' => false, 'reason' => 'Contenido inapropiado detectado: ' . $toxicity['reason'], 'score' => $toxicity['score']];
            }

            if ($this->textAnalysis->isSpam($content)) {
                return ['isValid' => false, 'reason' => 'El comentario parece ser spam.'];
            }
        }

        return ['isValid' => true, 'reason' => null];
    }

    public function addBannedWords(array $words): void
    {
        $this->bannedWords = array_merge($this->bannedWords, $words);
    }

    public function addBannedDomains(array $domains): void
    {
        $this->bannedDomains = array_merge($this->bannedDomains, $domains);
    }
}
