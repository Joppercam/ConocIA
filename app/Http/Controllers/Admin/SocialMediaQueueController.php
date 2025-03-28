<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SocialMediaQueueController extends Controller
{
    /**
     * Mostrar todas las publicaciones pendientes
     */
    public function index()
    {
        $pendingItems = SocialMediaQueue::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $publishedItems = SocialMediaQueue::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(10)
            ->get();
            
        $failedItems = SocialMediaQueue::where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Obtener noticias recientes para el selector de noticias
        $recentNews = \App\Models\News::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
            
        return view('admin.social-media.queue', compact(
            'pendingItems', 
            'publishedItems', 
            'failedItems',
            'recentNews'
        ));
    }
    
    /**
     * Marcar un ítem como publicado manualmente
     */
    public function markAsPublished(Request $request, $id)
    {
        $item = SocialMediaQueue::findOrFail($id);
        
        $request->validate([
            'post_id' => 'nullable|string',
            'post_url' => 'nullable|url'
        ]);
        
        try {
            $item->markAsPublished(
                $request->post_id, 
                $request->post_url
            );
            
            return redirect()->route('admin.social-media.queue')
                ->with('success', 'La publicación ha sido marcada como publicada.');
        } catch (\Exception $e) {
            Log::error('Error al marcar publicación como publicada: ' . $e->getMessage());
            
            return redirect()->route('admin.social-media.queue')
                ->with('error', 'Error al marcar la publicación: ' . $e->getMessage());
        }
    }
    
    /**
     * Eliminar un ítem de la cola
     */
    public function destroy($id)
    {
        $item = SocialMediaQueue::findOrFail($id);
        
        try {
            $item->delete();
            
            return redirect()->route('admin.social-media.queue')
                ->with('success', 'La publicación ha sido eliminada de la cola.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar publicación de la cola: ' . $e->getMessage());
            
            return redirect()->route('admin.social-media.queue')
                ->with('error', 'Error al eliminar la publicación: ' . $e->getMessage());
        }
    }



    /**
     * Almacena una nueva publicación manual en la cola
     */
    public function store(Request $request)
    {
        $request->validate([
            'network' => 'required|string|in:twitter,facebook,linkedin',
            'content' => 'required|string',
            'news_id' => 'nullable|exists:news,id'
        ]);
        
        try {
            $item = new SocialMediaQueue();
            $item->network = $request->network;
            $item->content = $request->content;
            $item->news_id = $request->news_id;
            $item->status = 'pending';
            
            // Obtener la URL de la noticia si existe
            $newsUrl = null;
            if ($request->news_id) {
                $news = \App\Models\News::find($request->news_id);
                if ($news) {
                    $newsUrl = route('news.show', $news->slug);
                }
            }
            
            // Generar URL para publicación manual con la noticia incluida
            if ($request->network == 'twitter') {
                $tweetText = $request->content;
                // Añadir la URL de la noticia al final del tweet si existe
                if ($newsUrl) {
                    // Verificar si hay espacio para añadir la URL (considerando que Twitter acorta URLs)
                    // Asumimos que Twitter acorta URLs a 23 caracteres aproximadamente
                    $remainingChars = 280 - strlen($tweetText) - 1; // -1 por el espacio
                    if ($remainingChars >= 23) {
                        $tweetText .= " " . $newsUrl;
                    }
                }
                $item->manual_url = "https://twitter.com/intent/tweet?text=" . urlencode($tweetText);
            } elseif ($request->network == 'facebook') {
                // Para Facebook, pasamos la URL de la noticia directamente si existe
                $shareUrl = $newsUrl ?: route('home');
                $item->manual_url = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($shareUrl);
            } elseif ($request->network == 'linkedin') {
                // Para LinkedIn, necesitamos asegurarnos de usar correctamente los parámetros
                $shareUrl = $newsUrl ?: route('home');
                
                // LinkedIn permite pasar título y texto para la publicación
                $linkedinParams = [
                    'url' => $shareUrl // URL de la noticia
                ];
                
                // Si hay contenido personalizado, lo agregamos como comentario
                if (!empty($request->content)) {
                    $linkedinParams['summary'] = $request->content;
                }
                
                // Si tenemos una noticia, agregamos su título
                if ($request->news_id && $news) {
                    $linkedinParams['title'] = $news->title;
                }
                
                // Construir la URL con todos los parámetros
                $item->manual_url = "https://www.linkedin.com/sharing/share-offsite/?" . 
                                        http_build_query($linkedinParams);
            }
            
            $item->save();
            
            return redirect()->route('admin.social-media.queue')
                ->with('success', 'Publicación añadida a la cola correctamente');
        } catch (\Exception $e) {
            Log::error('Error al crear publicación manual: ' . $e->getMessage());
            
            return redirect()->route('admin.social-media.queue')
                ->with('error', 'Error al crear la publicación: ' . $e->getMessage());
        }
    }
}