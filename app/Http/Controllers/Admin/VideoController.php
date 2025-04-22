<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoTag;
use App\Models\VideoPlatform;
use App\Services\Video\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Mostrar el listado de videos en el admin
     */
    public function index(Request $request)
    {
        // Filtros
        $query = Video::with(['platform', 'categories']);

        // Filtro por plataforma
        if ($request->filled('platform')) {
            $query->where('platform_id', $request->platform);
        }

        // Filtro por categoría
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('video_categories.id', $categoryId);
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%");
            });
        }

        // Obtener resultados paginados
        $videos = $query->orderBy('published_at', 'desc')->paginate(15);

        // Obtener plataformas y categorías para filtros
        $platforms = VideoPlatform::all();
        $categories = VideoCategory::all();

        return view('admin.videos.index', compact('videos', 'platforms', 'categories'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $platforms = VideoPlatform::all();
        $categories = VideoCategory::all();

        return view('admin.videos.create', compact('platforms', 'categories'));
    }

    /**
     * Almacenar un nuevo video
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform_id' => 'required|exists:video_platforms,id',
            'external_id' => 'required|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:video_categories,id',
            'tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Verificar si el video ya existe
            $existingVideo = Video::where('platform_id', $request->platform_id)
                ->where('external_id', $request->external_id)
                ->first();

            if ($existingVideo) {
                return redirect()->route('admin.videos.edit', $existingVideo->id)
                    ->with('info', 'El video ya existe en la base de datos y ha sido redirigido a la página de edición.');
            }

            // Obtener la plataforma
            $platform = VideoPlatform::find($request->platform_id);

            // Obtener datos del video desde la API correspondiente
            $videoService = "App\\Services\\Video\\" . ucfirst($platform->code) . "Service";
            $service = new $videoService();
            $videoInfo = $service->getVideoInfo($request->external_id);

            if (!$videoInfo) {
                return redirect()->back()
                    ->with('error', 'No se pudieron obtener los datos del video desde la plataforma. Verifica el ID del video.')
                    ->withInput();
            }

            // Crear el video
            $video = $this->videoService->createVideoFromData($videoInfo);

            // Asignar categorías
            if ($request->has('categories')) {
                $video->categories()->sync($request->categories);
            }

            // Asignar etiquetas
            if ($request->filled('tags')) {
                $tagNames = array_map('trim', explode(',', $request->tags));
                $tagIds = [];

                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;

                    $tag = VideoTag::firstOrCreate([
                        'name' => $tagName
                    ], [
                        'slug' => Str::slug($tagName)
                    ]);

                    $tagIds[] = $tag->id;
                }

                $video->tags()->sync($tagIds);
            }

            return redirect()->route('admin.videos.index')
                ->with('success', 'Video agregado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al crear video: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al crear el video: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $video = Video::with(['categories', 'tags', 'platform'])->findOrFail($id);
        $categories = VideoCategory::all();
        $videoCategories = $video->categories->pluck('id')->toArray();
        
        // Preparar tags como string para el campo de texto
        $videoTags = $video->tags->pluck('name')->implode(', ');

        return view('admin.videos.edit', compact('video', 'categories', 'videoCategories', 'videoTags'));
    }

    /**
     * Actualizar un video
     */
    public function update(Request $request, $id)
    {
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:video_categories,id',
            'tags' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $video = Video::findOrFail($id);
            
            // Actualizar datos básicos
            $video->title = $request->title;
            $video->description = $request->description;
            $video->is_featured = (bool) $request->input('is_featured', 0);
            $video->save();

            // Actualizar categorías
            if ($request->has('categories')) {
                $video->categories()->sync($request->categories);
            } else {
                $video->categories()->detach();
            }

            // Actualizar etiquetas
            if ($request->filled('tags')) {
                $tagNames = array_map('trim', explode(',', $request->tags));
                $tagIds = [];

                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;

                    $tag = VideoTag::firstOrCreate([
                        'name' => $tagName
                    ], [
                        'slug' => Str::slug($tagName)
                    ]);

                    $tagIds[] = $tag->id;
                }

                $video->tags()->sync($tagIds);
            } else {
                $video->tags()->detach();
            }

            return redirect()->route('admin.videos.index')
                ->with('success', 'Video actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar video: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al actualizar el video: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar un video
     */
    public function destroy($id)
    {
        try {
            $video = Video::findOrFail($id);
            $video->delete();

            return redirect()->route('admin.videos.index')
                ->with('success', 'Video eliminado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar video: ' . $e->getMessage());

            return redirect()->route('admin.videos.index')
                ->with('error', 'Error al eliminar el video: ' . $e->getMessage());
        }
    }

    /**
     * Importar video desde URL
     */
    public function importUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $url = $request->video_url;
            
            // Determinar plataforma y extraer ID del video
            $platformData = $this->detectPlatformFromUrl($url);
            
            if (!$platformData) {
                return redirect()->back()
                    ->with('error', 'URL no soportada. Por favor, introduce una URL válida de YouTube, Vimeo o Dailymotion.')
                    ->withInput();
            }
            
            // Verificar si el video ya existe
            $existingVideo = Video::where('platform_id', $platformData['platform_id'])
                ->where('external_id', $platformData['video_id'])
                ->first();

            if ($existingVideo) {
                return redirect()->route('admin.videos.edit', $existingVideo->id)
                    ->with('info', 'El video ya existe en la base de datos y ha sido redirigido a la página de edición.');
            }
            
            // Obtener datos del video desde la API correspondiente
            $platform = VideoPlatform::find($platformData['platform_id']);
            $videoService = "App\\Services\\Video\\" . ucfirst($platform->code) . "Service";
            $service = new $videoService();
            $videoInfo = $service->getVideoInfo($platformData['video_id']);

            if (!$videoInfo) {
                return redirect()->back()
                    ->with('error', 'No se pudieron obtener los datos del video desde la plataforma. Verifica la URL.')
                    ->withInput();
            }

            // Crear el video
            $video = $this->videoService->createVideoFromData($videoInfo);

            return redirect()->route('admin.videos.edit', $video->id)
                ->with('success', 'Video importado correctamente. Ahora puedes editar los detalles.');

        } catch (\Exception $e) {
            Log::error('Error al importar video desde URL: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al importar el video: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Importación masiva de videos
     */
    public function bulkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform_id' => 'required|exists:video_platforms,id',
            'keywords' => 'required|string',
            'category_id' => 'nullable|exists:video_categories,id',
            'limit' => 'required|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $platform = VideoPlatform::find($request->platform_id);
            $keywords = array_map('trim', explode(',', $request->keywords));
            $limit = $request->limit;
            
            // Obtener el servicio correspondiente
            $videoService = "App\\Services\\Video\\" . ucfirst($platform->code) . "Service";
            $service = new $videoService();
            
            // Buscar videos
            $videos = $service->search($keywords, $limit);
            
            if (empty($videos)) {
                return redirect()->back()
                    ->with('warning', 'No se encontraron videos con las palabras clave proporcionadas.')
                    ->withInput();
            }
            
            $importedCount = 0;
            $skippedCount = 0;
            
            foreach ($videos as $videoData) {
                // Verificar si el video ya existe
                $existingVideo = Video::where('platform_id', $videoData['platform_id'])
                    ->where('external_id', $videoData['external_id'])
                    ->first();
                
                if ($existingVideo) {
                    $skippedCount++;
                    continue;
                }
                
                // Crear el video
                $video = $this->videoService->createVideoFromData($videoData);
                
                // Asignar categoría si se especificó
                if ($request->filled('category_id')) {
                    $video->categories()->sync([$request->category_id]);
                }
                
                $importedCount++;
            }
            
            return redirect()->route('admin.videos.index')
                ->with('success', "Importación completada: {$importedCount} videos importados, {$skippedCount} videos omitidos (ya existían).");

        } catch (\Exception $e) {
            Log::error('Error en importación masiva de videos: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error en la importación masiva: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Detectar plataforma y extraer ID del video desde una URL
     */
    protected function detectPlatformFromUrl($url)
    {
        // Patrones para diferentes plataformas
        $patterns = [
            'youtube' => [
                // youtube.com/watch?v=VIDEO_ID
                '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
                // youtu.be/VIDEO_ID
                '/youtu\.be\/([a-zA-Z0-9_-]+)/',
                // youtube.com/embed/VIDEO_ID
                '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/'
            ],
            'vimeo' => [
                // vimeo.com/VIDEO_ID
                '/vimeo\.com\/([0-9]+)/',
                // player.vimeo.com/video/VIDEO_ID
                '/player\.vimeo\.com\/video\/([0-9]+)/'
            ],
            'dailymotion' => [
                // dailymotion.com/video/VIDEO_ID
                '/dailymotion\.com\/video\/([a-zA-Z0-9]+)/',
                // dai.ly/VIDEO_ID
                '/dai\.ly\/([a-zA-Z0-9]+)/'
            ]
        ];
        
        foreach ($patterns as $platform => $platformPatterns) {
            foreach ($platformPatterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    // Obtener ID de la plataforma
                    $platformObj = VideoPlatform::where('code', $platform)->first();
                    
                    if (!$platformObj) {
                        continue;
                    }
                    
                    return [
                        'platform_id' => $platformObj->id,
                        'video_id' => $matches[1]
                    ];
                }
            }
        }
        
        return null;
    }
}