<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'content' => 'required|string|min:5|max:1000',
            'save_info' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear nuevo comentario
        $comment = new Comment();
        $comment->commentable_type = $request->commentable_type;
        $comment->commentable_id = $request->commentable_id;
        $comment->guest_name = $request->guest_name;
        $comment->guest_email = $request->guest_email;
        $comment->content = $request->content;
        $comment->status = 'pending'; // Pendiente de moderación
        $comment->save();

        // Guardar información del usuario en cookies si se marca la casilla
        if ($request->has('save_info')) {
            Cookie::queue('comment_name', $request->guest_name, 43200); // 30 días
            Cookie::queue('comment_email', $request->guest_email, 43200); // 30 días
        } else {
            // Eliminar cookies si existe pero no está marcada la casilla
            Cookie::queue(Cookie::forget('comment_name'));
            Cookie::queue(Cookie::forget('comment_email'));
        }

        // Determinar la URL de retorno
        $returnUrl = $this->getReturnUrl($request);

        // Redirigir con mensaje de éxito y el ID del comentario para animación
        return redirect($returnUrl)
            ->with('success', 'Tu comentario ha sido enviado y está pendiente de aprobación.')
            ->with('comment_added', $comment->id);
    }

    /**
     * Obtiene la URL de retorno según el tipo de comentable
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function getReturnUrl(Request $request)
    {
        // Dividir el tipo de modelo para obtener solo el nombre de la clase
        $parts = explode('\\', $request->commentable_type);
        $modelType = strtolower(end($parts));

        // Determinar la URL de retorno según el tipo de modelo
        switch ($modelType) {
            case 'news':
                // Buscar la noticia para obtener su slug
                $news = app($request->commentable_type)::find($request->commentable_id);
                if ($news && isset($news->slug)) {
                    return route('news.show', $news->slug);
                }
                break;
                
            case 'column':
                // Buscar la columna para obtener su slug
                $column = app($request->commentable_type)::find($request->commentable_id);
                if ($column && isset($column->slug)) {
                    return route('columns.show', $column->slug);
                }
                break;
                
            // Agregar más casos para otros tipos de modelos comentables
        }

        // Si no se puede determinar la URL específica, volver a la página anterior
        return url()->previous();
    }

    /**
     * Aprobar un comentario (para administradores)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'approved';
        $comment->save();

        return back()->with('success', 'Comentario aprobado con éxito.');
    }

    /**
     * Rechazar un comentario (para administradores)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'rejected';
        $comment->save();

        return back()->with('success', 'Comentario rechazado con éxito.');
    }

    /**
     * Eliminar un comentario (para administradores)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Comentario eliminado con éxito.');
    }
}