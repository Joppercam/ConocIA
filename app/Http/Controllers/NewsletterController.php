<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class NewsletterController extends Controller
{
    /**
     * Procesa una nueva suscripción al newsletter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request)
    {
        try {
            // Registrar los datos recibidos para depuración
            Log::info('Datos de suscripción recibidos:', [
                'email' => $request->email,
                'categories' => $request->categories,
            ]);

            $request->validate([
                'email' => 'required|email|max:255',
                'name' => 'nullable|string|max:255',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id',
                'privacy_consent' => 'required|accepted',
            ]);

            // Iniciar transacción de base de datos
            DB::beginTransaction();

            // Verificar si el email ya está registrado
            $existing = Newsletter::where('email', $request->email)->first();

            if ($existing) {
                if ($existing->is_active) {
                    // Si el usuario ya está suscrito, actualizamos sus categorías
                    if ($request->has('categories')) {
                        $existing->categories()->sync($request->categories);
                    }
                    
                    // Actualizar el nombre si se proporcionó uno nuevo
                    if ($request->filled('name') && $existing->name !== $request->name) {
                        $existing->name = $request->name;
                        $existing->save();
                    }
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Ya estás suscrito. Hemos actualizado tus preferencias de categorías.'
                    ]);
                } else {
                    // Si el usuario se había dado de baja, lo reactivamos
                    $existing->is_active = true;
                    $existing->save();
                    
                    // Actualizamos sus categorías si se proporcionaron
                    if ($request->has('categories')) {
                        $existing->categories()->sync($request->categories);
                    }
                    
                    // Actualizar el nombre si se proporcionó uno nuevo
                    if ($request->filled('name')) {
                        $existing->name = $request->name;
                        $existing->save();
                    }
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Has sido reactivado en nuestra lista de suscriptores.'
                    ]);
                }
            }

            // Crear nuevo suscriptor
            // Generar token único
            $token = Str::random(64);
            
            // Crear registro
            $newsletter = Newsletter::create([
                'email' => $request->email,
                'name' => $request->name,
                'token' => $token,
                'is_active' => true,
            ]);
            
            // Añadir categorías seleccionadas
            if ($request->has('categories')) {
                $newsletter->categories()->attach($request->categories);
            }
            
            DB::commit();
            
            Log::info('Suscripción exitosa para: ' . $request->email);
            
            return response()->json([
                'success' => true,
                'message' => 'Te has suscrito correctamente a nuestro newsletter.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('Error de validación: ' . $e->getMessage(), [
                'errors' => $e->errors(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor verifica los datos ingresados.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error al procesar suscripción: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar tu solicitud. Por favor inténtalo de nuevo.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Cancela la suscripción de un usuario.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe($token)
    {
        $subscriber = Newsletter::where('token', $token)->first();
        
        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'El enlace de cancelación no es válido o ha expirado.');
        }
        
        // Desactivar suscripción
        $subscriber->is_active = false;
        $subscriber->save();
        
        return redirect()->route('home')->with('info', 'Tu suscripción ha sido cancelada. Lamentamos verte partir.');
    }
}