<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\ResearchSubmitController;
use App\Http\Controllers\GuestPostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\ResearchController as AdminResearchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\NewsletterAdminController;
use App\Http\Controllers\Admin\NewsApiController;
use App\Http\Controllers\NewsletterSendController;
use App\Http\Controllers\Admin\ColumnController as AdminColumnController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\TikTokController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\SpotifyIntegrationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/



// Rutas para la sección de videos - Versión corregida con namespaces completos
Route::prefix('videos')->name('videos.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VideoController::class, 'index'])->name('index');
    Route::get('/featured', [\App\Http\Controllers\VideoController::class, 'featured'])->name('featured');
    Route::get('/popular', [\App\Http\Controllers\VideoController::class, 'popular'])->name('popular');
    Route::get('/category/{category}', [\App\Http\Controllers\VideoController::class, 'byCategory'])->name('category');
    Route::get('/tag/{tag}', [\App\Http\Controllers\VideoController::class, 'byTag'])->name('tag');
    Route::get('/{id}', [\App\Http\Controllers\VideoController::class, 'show'])->name('show');
    
    // Ruta para comentarios (requiere autenticación)
    Route::post('/{id}/comment', [\App\Http\Controllers\VideoController::class, 'storeComment'])->name('comment')->middleware('auth');
});

// Rutas API para videos
Route::prefix('api/videos')->name('api.videos.')->group(function () {
    Route::get('/by-category/{categoryId}', [\App\Http\Controllers\VideoController::class, 'apiGetVideosByCategory'])->name('by-category');
    Route::get('/news-recommendations', [\App\Http\Controllers\VideoController::class, 'apiGetNewsRecommendations'])->name('news-recommendations');
});

// Rutas para sitemaps
Route::get('sitemap.xml', [SitemapController::class, 'index']);

// Sitemap principal generado programáticamente
Route::get('sitemap-main.xml', function() {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>daily</changefreq>';
    $xml .= '<priority>1.0</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/news') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>daily</changefreq>';
    $xml .= '<priority>0.9</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/investigacion') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>weekly</changefreq>';
    $xml .= '<priority>0.8</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/columnas') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>weekly</changefreq>';
    $xml .= '<priority>0.8</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/acerca-de') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>monthly</changefreq>';
    $xml .= '<priority>0.5</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/contacto') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>monthly</changefreq>';
    $xml .= '<priority>0.5</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/privacidad') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>yearly</changefreq>';
    $xml .= '<priority>0.3</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/terminos') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>yearly</changefreq>';
    $xml .= '<priority>0.3</priority>';
    $xml .= '</url>';
    
    $xml .= '<url>';
    $xml .= '<loc>' . url('/cookies') . '</loc>';
    $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
    $xml .= '<changefreq>yearly</changefreq>';
    $xml .= '<priority>0.3</priority>';
    $xml .= '</url>';
    
    $xml .= '</urlset>';
    
    return response($xml)->header('Content-Type', 'text/xml');
});

Route::get('sitemap-news.xml', [SitemapController::class, 'news']);
Route::get('sitemap-categories.xml', [SitemapController::class, 'categories']);
Route::get('sitemap-research.xml', [SitemapController::class, 'research']);
Route::get('sitemap-columns.xml', [SitemapController::class, 'columns']);

// Rutas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/acerca-de', [HomeController::class, 'about'])->name('about');
Route::get('/contacto', [HomeController::class, 'contact'])->name('contact');
Route::post('/contacto', [HomeController::class, 'sendContact'])->name('contact.send');
Route::get('/buscar', [HomeController::class, 'search'])->name('search');

// Rutas para noticias
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/news/category/{slug}', [NewsController::class, 'category'])->name('news.category');
Route::get('/category/{slug}', [NewsController::class, 'byCategory'])->name('news.by.category');
Route::get('/news/tag/{tag}', [NewsController::class, 'byTag'])->name('news.by.tag');

// Rutas para investigación
Route::get('/investigacion', [ResearchController::class, 'index'])->name('research.index');
Route::get('/investigacion/{id}', [ResearchController::class, 'show'])->name('research.show');
Route::get('/research/type/{type}', [ResearchController::class, 'byType'])->name('research.type');
Route::get('/submit-research', [ResearchController::class, 'create'])->name('submit-research');


// Modificar las rutas de envío de investigación para eliminar el middleware 'auth'
Route::get('/submit-research', [ResearchSubmitController::class, 'create'])->name('submit-research');
Route::post('/submit-research', [ResearchSubmitController::class, 'store'])->name('research.store');


// Ruta para newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Ruta para autores
Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

// Ruta para comentarios
Route::post('/comments', [CommentController::class, 'store']);

// Rutas para el frontend
Route::get('/columnas', [ColumnController::class, 'index'])->name('columns.index');
Route::get('/columnas/{slug}', [ColumnController::class, 'show'])->name('columns.show');

// Rutas para publicaciones de invitados
Route::prefix('colaboraciones')->group(function () {
    Route::get('/', [GuestPostController::class, 'index'])->name('guest-posts.index');
    Route::get('/crear', [GuestPostController::class, 'create'])->middleware('auth')->name('guest-posts.create');
    Route::post('/', [GuestPostController::class, 'store'])->middleware('auth')->name('guest-posts.store');
    Route::get('/{slug}', [GuestPostController::class, 'show'])->name('guest-posts.show');
    Route::get('/categoria/{slug}', [GuestPostController::class, 'byCategory'])->name('guest-posts.category');
    Route::get('/etiqueta/{slug}', [GuestPostController::class, 'byTag'])->name('guest-posts.tag');
    Route::post('/{slug}/comentarios', [GuestPostController::class, 'storeComment'])->name('guest-posts.comments.store');
});

// Autenticación personalizada
Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/registro', [AuthController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('/registro', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// Rutas para el perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Para el frontend
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');


Route::get('/eventos', 'EventController@index')->name('events.index');
Route::get('/sobre-nosotros/colaboradores', 'AboutController@contributors')->name('about.contributors');
//Route::get('/paginas/privacidad', 'PageController@privacy')->name('pages.privacy');
//Route::get('/paginas/terminos', 'PageController@terms')->name('pages.terms');
//Route::get('/paginas/cookies', 'PageController@cookies')->name('pages.cookies');

Route::get('investigacion/categoria/{category:slug}', [App\Http\Controllers\ResearchController::class, 'category'])
    ->name('research.category');
    

// Rutas para páginas legales
Route::get('/privacidad', [App\Http\Controllers\PagesController::class, 'privacy'])->name('pages.privacy');
Route::get('/terminos', [App\Http\Controllers\PagesController::class, 'terms'])->name('pages.terms');
Route::get('/cookies', [App\Http\Controllers\PagesController::class, 'cookies'])->name('pages.cookies');    


// Rutas para el módulo TikTok en el panel de administración
Route::prefix('admin/tiktok')->name('admin.tiktok.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    
    // Dashboard principal
    Route::get('/', [TikTokController::class, 'index'])->name('index');
    
    // Recomendaciones de artículos
    Route::get('/recommendations', [TikTokController::class, 'recommendations'])->name('recommendations');
    
    // Formulario para crear un guión manualmente
    Route::get('/create/{articleId}', [TikTokController::class, 'create'])->name('create');
    
    // Guardar guión creado manualmente
    Route::post('/store', [TikTokController::class, 'store'])->name('store');
    
    // Generar guión automáticamente
    Route::get('/generate/{articleId}', [TikTokController::class, 'generate'])->name('generate');
    
    // Editar guión
    Route::get('/edit/{id}', [TikTokController::class, 'edit'])->name('edit');
    
    // Actualizar guión
    Route::put('/update/{id}', [TikTokController::class, 'update'])->name('update');
    
    // Actualizar estado del guión
    Route::put('/update-status/{id}', [TikTokController::class, 'updateStatus'])->name('update-status');
    
    // Registrar métricas
    Route::post('/record-metrics/{id}', [TikTokController::class, 'recordMetrics'])->name('record-metrics');
    
    // Estadísticas
    Route::get('/stats', [TikTokController::class, 'stats'])->name('stats');
    
    // Página de ayuda
    Route::get('/help', function () {
        return view('admin.tiktok.help');
    })->name('help');
});


// Rutas de administración
Route::prefix('admin')->name('admin.')->group(function () {
    // Rutas para usuarios no autenticados
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    // Rutas protegidas por autenticación y middleware admin
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Cerrar sesión
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Gestión básica de noticias (CRUD)
        Route::resource('news', AdminNewsController::class);
        
        // Rutas adicionales para noticias
        Route::post('news/bulk-actions', [AdminNewsController::class, 'bulkActions'])->name('news.bulk-actions');
        Route::get('news/export', [AdminNewsController::class, 'export'])->name('news.export');
        Route::post('news/upload-image', [AdminNewsController::class, 'uploadImage'])->name('news.upload-image');
        Route::get('news/preview/{news}', [AdminNewsController::class, 'preview'])->name('news.preview');
        Route::post('news/restore/{id}', [AdminNewsController::class, 'restore'])->name('news.restore');
        Route::get('news/trashed', [AdminNewsController::class, 'trashed'])->name('news.trashed');
        Route::delete('news/force-delete/{id}', [AdminNewsController::class, 'forceDelete'])->name('news.force-delete');
        
        // Gestión de investigaciones
        Route::resource('research', AdminResearchController::class);
        Route::post('research/bulk-actions', [AdminResearchController::class, 'bulkActions'])->name('research.bulk-actions');
        Route::get('research/export', [AdminResearchController::class, 'export'])->name('research.export');
        Route::post('research/upload-image', [AdminResearchController::class, 'uploadImage'])->name('research.upload-image');
        Route::get('research/preview/{research}', [AdminResearchController::class, 'preview'])->name('research.preview');
        Route::resource('categories', CategoryController::class);

        // Publicaciones de invitados
        Route::get('colaboraciones/pendientes', [AdminResearchController::class, 'pendingPosts'])->name('invitados.pending');
        Route::post('colaboraciones/{id}/aprobar', [AdminResearchController::class, 'approvePost'])->name('invitados.approve');
        Route::post('colaboraciones/{id}/rechazar', [AdminResearchController::class, 'rejectPost'])->name('invitados.reject');
  
        // Lista principal de comentarios
        Route::get('/comments', [App\Http\Controllers\Admin\CommentController::class, 'index'])->name('comments.index');
            
        // Filtrar por estado
        Route::get('/comments/pending', [App\Http\Controllers\Admin\CommentController::class, 'pending'])->name('comments.pending');
        Route::get('/comments/approved', [App\Http\Controllers\Admin\CommentController::class, 'approved'])->name('comments.approved');
        Route::get('/comments/rejected', [App\Http\Controllers\Admin\CommentController::class, 'rejected'])->name('comments.rejected');

        // Acciones sobre los comentarios
        Route::patch('/comments/{comment}/approve', [App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/comments/{comment}/reject', [App\Http\Controllers\Admin\CommentController::class, 'reject'])->name('comments.reject');
        Route::delete('/comments/{comment}', [App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('comments.destroy');
        Route::delete('comments/{comment}', 'App\Http\Controllers\Admin\CommentController@destroy')->name('comments.delete');
        Route::delete('comments/{comment}', 'App\Http\Controllers\Admin\CommentController@destroy')->name('comments.destroy');
        
        // Respuesta a comentarios desde el panel de administración
        Route::post('/comments/{comment}/reply', [App\Http\Controllers\Admin\CommentController::class, 'reply'])->name('comments.reply');

        Route::resource('users', UserController::class);
      

        // Rutas para ejecutar API de noticias
        Route::get('/api', [NewsApiController::class, 'index'])->name('api.index');
        Route::post('/api/execute', [NewsApiController::class, 'execute'])->name('api.execute');

        Route::resource('columns', AdminColumnController::class);


        Route::prefix('social-media')->name('social-media.')->group(function () {
            // Cambiar esta línea
            Route::get('/queue', [App\Http\Controllers\Admin\SocialMediaQueueController::class, 'index'])
                ->name('queue'); // En lugar de queue.index
            
            // Las otras rutas permanecen igual
            Route::post('/queue/{id}/mark-published', [App\Http\Controllers\Admin\SocialMediaQueueController::class, 'markAsPublished'])
                ->name('queue.mark-published');
            
            Route::delete('/queue/{id}', [App\Http\Controllers\Admin\SocialMediaQueueController::class, 'destroy'])
                ->name('queue.destroy');
            
            Route::post('/queue', [App\Http\Controllers\Admin\SocialMediaQueueController::class, 'store'])
                ->name('queue.store');
        });


       
         // Rutas para gestionar videos
        Route::prefix('videos')->name('videos.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\VideoController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\VideoController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\VideoController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\Admin\VideoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\Admin\VideoController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\VideoController::class, 'destroy'])->name('destroy');
            
            // Importación de videos
            Route::post('/import-url', [App\Http\Controllers\Admin\VideoController::class, 'importUrl'])->name('import-url');
            Route::post('/bulk-import', [App\Http\Controllers\Admin\VideoController::class, 'bulkImport'])->name('bulk-import');
            
            // Rutas para categorías de videos
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\VideoCategoryController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Admin\VideoCategoryController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Admin\VideoCategoryController::class, 'store'])->name('store');
                Route::get('/{category}/edit', [App\Http\Controllers\Admin\VideoCategoryController::class, 'edit'])->name('edit');
                Route::put('/{category}', [App\Http\Controllers\Admin\VideoCategoryController::class, 'update'])->name('update');
                Route::delete('/{category}', [App\Http\Controllers\Admin\VideoCategoryController::class, 'destroy'])->name('destroy');
            });
            
            // Rutas para plataformas de videos
            Route::prefix('platforms')->name('platforms.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\VideoPlatformController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Admin\VideoPlatformController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Admin\VideoPlatformController::class, 'store'])->name('store');
                Route::get('/{platform}/edit', [App\Http\Controllers\Admin\VideoPlatformController::class, 'edit'])->name('edit');
                Route::put('/{platform}', [App\Http\Controllers\Admin\VideoPlatformController::class, 'update'])->name('update');
                Route::delete('/{platform}', [App\Http\Controllers\Admin\VideoPlatformController::class, 'destroy'])->name('destroy');
            });
        });
    });


});

// Rutas para podcasts
Route::prefix('podcasts')->name('podcasts.')->group(function() {
    Route::get('/', [App\Http\Controllers\PodcastController::class, 'index'])->name('index');
    Route::get('/{podcast}', [App\Http\Controllers\PodcastController::class, 'show'])->name('show');
    Route::post('/{podcast}/play', [App\Http\Controllers\PodcastController::class, 'registerPlay'])->name('play');
});

// Rutas de podcasts públicas
Route::get('/podcasts-populares', [PodcastController::class, 'popular'])->name('podcasts.popular');
Route::get('/podcasts-feed', [PodcastController::class, 'feed'])->name('podcasts.feed');

// Las rutas de administración de podcasts deben permanecer dentro del grupo admin
Route::prefix('admin')->name('admin.')->group(function () {
    // ... Otras rutas de admin
    
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // ... Otras rutas protegidas
        
        // Rutas de administración de podcasts
        Route::resource('podcasts', \App\Http\Controllers\Admin\PodcastController::class);
        Route::post('podcasts/generate', [\App\Http\Controllers\Admin\PodcastController::class, 'generatePodcasts'])
            ->name('podcasts.generate');
    });
});
// Agrega estas rutas antes del grupo de middleware auth
Route::get('/admin/spotify/callback', [\App\Http\Controllers\Admin\SpotifyIntegrationController::class, 'handleSpotifyCallback'])->name('admin.spotify.callback');

// Rutas para integración con Spotify - VERSIÓN CORREGIDA
Route::prefix('admin/spotify')->name('admin.spotify.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\SpotifyController::class, 'dashboard'])->name('dashboard');
    Route::get('/share/{podcast}', [\App\Http\Controllers\Admin\SpotifyController::class, 'share'])->name('share');
    
    // Las otras rutas que tenías antes
    Route::get('/authorize', [\App\Http\Controllers\Admin\SpotifyIntegrationController::class, 'authorizeSpotify'])->name('authorize');
    Route::post('/upload/{podcast}', [\App\Http\Controllers\Admin\SpotifyIntegrationController::class, 'uploadPodcast'])->name('upload');
  
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', \App\Http\Middleware\AdminMiddleware::class]], function () {
    // Rutas más específicas primero
    Route::get('newsletter/send', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'showSendForm'])->name('admin.newsletter.send');
    Route::post('newsletter/send', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'sendNewsletter'])->name('admin.newsletter.send.post');
    
    // Luego las rutas con parámetros específicos
    Route::get('newsletter/{newsletter}/edit', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'edit'])->name('admin.newsletter.edit');
    Route::put('newsletter/{newsletter}', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'update'])->name('admin.newsletter.update');
    Route::delete('newsletter/{newsletter}', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'destroy'])->name('admin.newsletter.destroy');
    Route::patch('newsletter/{newsletter}/toggle', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'toggleActive'])->name('admin.newsletter.toggle');
    
    // Finalmente las rutas generales
    Route::get('newsletter', [\App\Http\Controllers\Admin\NewsletterAdminController::class, 'index'])->name('admin.newsletter.index');
});