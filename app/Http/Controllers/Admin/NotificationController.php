<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mostrar todas las notificaciones.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.notifications.index', compact('notifications'));
    }
    
    /**
     * Obtener notificaciones vía AJAX para el dropdown.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        
        // Obtener todas las notificaciones no leídas para mostrar en el dropdown
        $notifications = $user->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Obtener el conteo total de notificaciones no leídas
        $count = $user->notifications()->unread()->count();
        
        // Si hay un ID de última notificación vista, filtrar solo las nuevas
        $lastId = $request->input('last_id', 0);
        $newNotifications = [];
        
        if ($lastId > 0) {
            $newNotifications = $user->notifications()
                ->where('id', '>', $lastId)
                ->get();
        }
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $count,
            'new_notifications' => $newNotifications
        ]);
    }
    
    /**
     * Marcar una notificación como leída.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Verificar que la notificación pertenezca al usuario actual
        if ($notification->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }
        
        $notification->markAsRead();
        
        return back()->with('success', 'Notificación marcada como leída.');
    }
    
    /**
     * Marcar todas las notificaciones como leídas.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->unread()
            ->update(['read_at' => now()]);
            
        return back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }
    
    /**
     * Eliminar una notificación.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Verificar que la notificación pertenezca al usuario actual
        if ($notification->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }
        
        $notification->delete();
        
        return back()->with('success', 'Notificación eliminada con éxito.');
    }
}