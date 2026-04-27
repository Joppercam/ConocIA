<?php

namespace App\Http\Middleware;

use App\Support\MetricsTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            MetricsTracker::track('premium_access_attempt', [
                'feature' => $feature,
                'result' => 'guest_redirect',
                'url' => $request->fullUrl(),
            ]);

            return redirect()->route('login')->with('error', 'Inicia sesión para acceder a esta función.');
        }

        if (!$user->canAccessFeature($feature)) {
            MetricsTracker::track('premium_access_attempt', [
                'feature' => $feature,
                'result' => 'blocked',
                'plan' => $user->plan(),
                'url' => $request->fullUrl(),
            ]);

            return redirect()->route('billing.plans')->with('error', 'Actualiza tu plan para acceder a esta función.');
        }

        return $next($request);
    }
}
