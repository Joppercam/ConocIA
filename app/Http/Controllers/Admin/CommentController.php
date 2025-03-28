<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\NotificationController as Notification;

class CommentController extends Controller
{
    /**
     * Mostrar todos los comentarios
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $comments = Comment::with(['commentable'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.comments.index', [
            'comments' => $comments,
            'title' => 'Todos los comentarios',
            'activeTab' => 'all'
        ]);
    }
    
    /**
     * Mostrar comentarios pendientes
     *
     * @return \Illuminate\View\View
     */
    public function pending()
    {
        $comments = Comment::with(['commentable'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.comments.index', [
            'comments' => $comments,
            'title' => 'Comentarios pendientes',
            'activeTab' => 'pending'
        ]);
    }
    
    /**
     * Mostrar comentarios aprobados
     *
     * @return \Illuminate\View\View
     */
    public function approved()
    {
        $comments = Comment::with(['commentable'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.comments.index', [
            'comments' => $comments,
            'title' => 'Comentarios aprobados',
            'activeTab' => 'approved'
        ]);
    }
    
    /**
     * Mostrar comentarios rechazados
     *
     * @return \Illuminate\View\View
     */
    public function rejected()
    {
        $comments = Comment::with(['commentable'])
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.comments.index', [
            'comments' => $comments,
            'title' => 'Comentarios rechazados',
            'activeTab' => 'rejected'
        ]);
    }

    /**
     * Aprobar un comentario
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'approved';
        $comment->save();

        return back()->with('success', 'Comentario aprobado con éxito.');
    }

    /**
     * Rechazar un comentario
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'rejected';
        $comment->save();

        return back()->with('success', 'Comentario rechazado con éxito.');
    }

    /**
     * Eliminar un comentario
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Comentario eliminado con éxito.');
    }
    
    /**
     * Responder a un comentario
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, $id)
    {
        $parentComment = Comment::findOrFail($id);
        
        // Validar el contenido de la respuesta
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);
        
        // Crear la respuesta como un comentario hijo
        $reply = new Comment();
        $reply->commentable_type = $parentComment->commentable_type;
        $reply->commentable_id = $parentComment->commentable_id;
        $reply->user_id = Auth::id();
        $reply->content = $request->content;
        $reply->status = 'approved'; // Las respuestas del administrador se aprueban automáticamente
        $reply->parent_id = $parentComment->id;
        $reply->save();
        
        return back()->with('success', 'Respuesta enviada con éxito.');
    }




    /**
     * Aprobar un comentario
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'approved';
        $comment->save();
        
        // Notificar al autor del comentario si es un usuario registrado
        if ($comment->user_id) {
            Notification::create([
                'user_id' => $comment->user_id,
                'type' => 'comment_approved',
                'data' => [
                    'comment_id' => $comment->id,
                    'content' => \Str::limit($comment->content, 100),
                    'article_title' => $comment->commentable ? $comment->commentable->title : 'Artículo no disponible',
                    'article_slug' => $comment->commentable ? $comment->commentable->slug : '',
                ]
            ]);
        }

        return back()->with('success', 'Comentario aprobado con éxito.');
    }

    /**
     * Rechazar un comentario
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'rejected';
        $comment->save();
        
        // Opcionalmente, notificar al autor del comentario si es un usuario registrado
        if ($comment->user_id) {
            Notification::create([
                'user_id' => $comment->user_id,
                'type' => 'comment_rejected',
                'data' => [
                    'comment_id' => $comment->id,
                    'content' => \Str::limit($comment->content, 100),
                    'article_title' => $comment->commentable ? $comment->commentable->title : 'Artículo no disponible',
                    'reject_reason' => 'El comentario no cumple con nuestras normas de comunidad.', // Mensaje predeterminado
                ]
            ]);
        }

        return back()->with('success', 'Comentario rechazado con éxito.');
    }

    /**
     * Responder a un comentario
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, $id)
    {
        $parentComment = Comment::findOrFail($id);
        
        // Validar el contenido de la respuesta
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);
        
        // Crear la respuesta como un comentario hijo
        $reply = new Comment();
        $reply->commentable_type = $parentComment->commentable_type;
        $reply->commentable_id = $parentComment->commentable_id;
        $reply->user_id = Auth::id();
        $reply->content = $request->content;
        $reply->status = 'approved'; // Las respuestas del administrador se aprueban automáticamente
        $reply->parent_id = $parentComment->id;
        $reply->save();
        
        // Notificar al autor del comentario original si es un usuario registrado
        if ($parentComment->user_id) {
            Notification::create([
                'user_id' => $parentComment->user_id,
                'type' => 'comment_reply',
                'data' => [
                    'comment_id' => $parentComment->id,
                    'reply_id' => $reply->id,
                    'reply_content' => \Str::limit($reply->content, 100),
                    'article_title' => $parentComment->commentable ? $parentComment->commentable->title : 'Artículo no disponible',
                    'article_slug' => $parentComment->commentable ? $parentComment->commentable->slug : '',
                    'admin_name' => Auth::user()->name,
                ]
            ]);
        }
        
        return back()->with('success', 'Respuesta enviada con éxito.');
    }
}