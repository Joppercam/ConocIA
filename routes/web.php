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
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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
Route::get('/paginas/privacidad', 'PageController@privacy')->name('pages.privacy');
Route::get('/paginas/terminos', 'PageController@terms')->name('pages.terms');
Route::get('/paginas/cookies', 'PageController@cookies')->name('pages.cookies');

Route::get('investigacion/categoria/{category:slug}', [App\Http\Controllers\ResearchController::class, 'category'])
    ->name('research.category');
    

// Rutas para páginas legales
Route::get('/privacidad', [App\Http\Controllers\PagesController::class, 'privacy'])->name('pages.privacy');
Route::get('/terminos', [App\Http\Controllers\PagesController::class, 'terms'])->name('pages.terms');
Route::get('/cookies', [App\Http\Controllers\PagesController::class, 'cookies'])->name('pages.cookies');    

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
        Route::resource('newsletter', NewsletterController::class)->only(['index', 'destroy']);

        // Rutas para administrar newsletter
        Route::get('/newsletter', [NewsletterAdminController::class, 'index'])->name('newsletter.index');
        Route::delete('/newsletter/{newsletter}', [NewsletterAdminController::class, 'destroy'])->name('newsletter.destroy');
        Route::patch('/newsletter/{newsletter}/toggle', [NewsletterAdminController::class, 'toggleActive'])->name('newsletter.toggle');

           // Rutas para administrar y enviar newsletter
        Route::get('/newsletter/send', [NewsletterSendController::class, 'form'])->name('newsletter.send');
        Route::post('/newsletter/send', [NewsletterSendController::class, 'send'])->name('newsletter.send.post');

        // Rutas para ejecutar API de noticias
        Route::get('/api', [NewsApiController::class, 'index'])->name('api.index');
        Route::post('/api/execute', [NewsApiController::class, 'execute'])->name('api.execute');

        Route::resource('columns', AdminColumnController::class);

    });
});