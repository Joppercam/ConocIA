<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterConfirmationMail;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $existing = Newsletter::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active) {
                return $this->respond($request, false, 'Este correo ya está suscrito a nuestro newsletter.', true);
            }

            // Reenviar confirmación si aún no verificó
            $existing->update(['token' => Str::random(40)]);
            $this->sendConfirmationEmail($existing);

            return $this->respond($request, true, 'Te enviamos un nuevo correo de confirmación. Revisa tu bandeja de entrada.');
        }

        try {
            $subscriber = Newsletter::create([
                'email'     => $email,
                'is_active' => false,
                'token'     => Str::random(40),
            ]);

            $this->sendConfirmationEmail($subscriber);

            return $this->respond($request, true, '¡Casi listo! Te enviamos un correo para confirmar tu suscripción.');
        } catch (\Exception $e) {
            Log::error('Error al suscribir newsletter: ' . $e->getMessage());
            return $this->respond($request, false, 'Hubo un problema. Por favor intenta nuevamente.');
        }
    }

    public function confirm($token)
    {
        $subscriber = Newsletter::where('token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Enlace inválido o expirado.');
        }

        if ($subscriber->is_active) {
            return redirect()->route('home')->with('info', 'Tu suscripción ya estaba confirmada.');
        }

        $subscriber->update([
            'is_active'   => true,
            'verified_at' => now(),
        ]);

        return redirect()->route('home')->with('subscription_success', '¡Suscripción confirmada! Bienvenido a ConocIA.');
    }

    public function unsubscribe($token)
    {
        $subscriber = Newsletter::where('token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Enlace inválido.');
        }

        $subscriber->update(['is_active' => false]);

        return redirect()->route('home')->with('success', 'Te has dado de baja correctamente.');
    }

    private function sendConfirmationEmail(Newsletter $subscriber): void
    {
        $confirmationUrl = route('newsletter.confirm', $subscriber->token);
        $unsubscribeUrl  = route('newsletter.unsubscribe', $subscriber->token);

        Mail::to($subscriber->email)->send(
            new NewsletterConfirmationMail($confirmationUrl, $unsubscribeUrl)
        );
    }

    private function respond(Request $request, bool $success, string $message, bool $info = false)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(compact('success', 'message', 'info'));
        }

        $flashKey = $success ? 'subscription_success' : ($info ? 'subscription_info' : 'subscription_error');
        return redirect()->back()->with($flashKey, $message);
    }
}
