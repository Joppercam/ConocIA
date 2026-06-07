<?php

namespace App\Services;

class TtsTextCleaner
{
    /**
     * Prepara texto plano para enviarlo a Google TTS.
     * Corrige pronunciación de la marca, elimina URLs, JSON y artefactos de markdown.
     */
    public static function clean(string $text): string
    {
        // Decodificar entidades HTML y quitar etiquetas
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Marca: "ConocIA.cl" / "ConocIA" → pronunciación natural en español
        $text = str_replace(
            ['ConocIA.cl', 'conocia.cl', 'ConocIA', 'CONOCIA'],
            ['Conocia punto cl', 'conocia punto cl', 'Conocia', 'Conocia'],
            $text
        );

        // Sección de fuentes/footnotes: eliminar todo tras "Fuentes", "Referencias", "Sources"
        $text = preg_replace('/\n?\s*(Fuentes|Referencias|Sources)\s*\n.*/su', '', $text);

        // Code blocks triple backtick (antes que nada, pueden contener URLs y JSON)
        $text = preg_replace('/```[\s\S]*?```/u', '', $text);

        // Markdown links: [texto](url) → conservar solo el texto
        $text = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $text);

        // Eliminar URLs con protocolo (http/https/ftp)
        $text = preg_replace('/https?:\/\/[^\s\]})>"\']+/u', '', $text);
        $text = preg_replace('/ftp:\/\/[^\s\]})>"\']+/u', '', $text);

        // Eliminar URLs sin protocolo: www.algo.tld
        $text = preg_replace('/\bwww\.[a-z0-9.-]+\.[a-z]{2,}\b/iu', '', $text);

        // Eliminar JSON inline: objetos { } y arrays [ ] que claramente son datos
        $text = preg_replace('/\{[^{}]{0,1000}\}/s', '', $text);
        $text = preg_replace('/\[[^\[\]]{0,1000}\]/s', '', $text);

        // Eliminar markdown: **, *, ##, -, numeraciones, backticks
        $text = preg_replace('/\*\*([^*]+)\*\*/u', '$1', $text);
        $text = preg_replace('/\*([^*\n]+)\*/u', '$1', $text);
        $text = preg_replace('/#{1,6}\s*/u', '', $text);
        $text = preg_replace('/`[^`]*`/u', '', $text);
        $text = preg_replace('/^[-*+]\s+/mu', '', $text);
        $text = preg_replace('/^\d+\.\s+/mu', '', $text);

        // Eliminar referencias tipo [1], [2], (1), (ver nota 3)
        $text = preg_replace('/\[\d+\]|\(\d+\)|\(ver nota \d+\)/u', '', $text);

        // Números de nota sueltos pegados a palabras: "... IA.1 ..." → "... IA. ..."
        $text = preg_replace('/([.!?,;:])(\d+)(\s)/u', '$1$3', $text);

        // Dominios sueltos que quedaron: "example.com" → "example punto com"
        // TLDs ampliados para cubrir más casos reales
        $text = preg_replace(
            '/\b([a-z0-9-]+)\.(cl|com|org|net|io|ai|es|ar|uk|edu|gov|info|co|mx|br|de|fr|jp)\b/iu',
            '$1 punto $2',
            $text
        );

        // Paréntesis y corchetes vacíos que quedan tras limpiar URLs y código
        $text = preg_replace('/\(\s*\)|\[\s*\]/u', '', $text);

        // Símbolos comunes
        $text = str_replace(['%', '&amp;', '&gt;', '&lt;', '&'], [' por ciento', ' y ', '>', '<', ' y '], $text);

        // Emojis y caracteres de control
        $text = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2700}-\x{27BF}\x{FE00}-\x{FEFF}]/u', '', $text);

        // Colapsar espacios y saltos de línea múltiples
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}
