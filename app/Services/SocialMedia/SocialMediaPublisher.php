<?php

namespace App\Services\SocialMedia;

use App\Models\News;

interface SocialMediaPublisher
{
    /**
     * Publica un artículo de noticias en la red social.
     *
     * @param News $article El artículo a publicar
     * @return array Resultado de la publicación con las siguientes claves:
     *               - success: bool - Indica si la publicación fue exitosa
     *               - post_id: string|null - ID del post en la red social
     *               - post_url: string|null - URL del post en la red social
     *               - message: string|null - Mensaje de error si success es false
     */
    public function publish(News $article): array;
    
    /**
     * Verifica si la conexión con la red social está configurada y es válida.
     *
     * @return bool
     */
    public function isConfigured(): bool;
    
    /**
     * Formatea el contenido del artículo para la red social específica.
     *
     * @param News $article El artículo a formatear
     * @return array Datos formateados con las siguientes claves:
     *               - text: string - Texto del post
     *               - media: array - Lista de archivos multimedia a adjuntar
     *               - link: string|null - Enlace a incluir
     */
    public function formatContent(News $article): array;
}