<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Support\MetricsTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BillingController extends Controller
{
    public function plans(Request $request)
    {
        MetricsTracker::track('plans_view', [
            'plan' => $request->user()?->plan() ?? 'guest',
        ]);

        return view('billing.plans');
    }

    public function select(Request $request, string $plan)
    {
        abort_unless(in_array($plan, ['free', 'pro', 'business'], true), 404);

        $user = $request->user();

        if (!$user) {
            MetricsTracker::track('click_upgrade', ['plan' => $plan, 'result' => 'guest']);

            return redirect()->route('login')->with('error', 'Inicia sesión para actualizar tu plan.');
        }

        MetricsTracker::track('click_upgrade', [
            'plan' => $plan,
            'current_plan' => $user->plan(),
        ]);

        if (!Schema::hasColumn('users', 'plan_actual')) {
            return redirect()->route('billing.plans')
                ->with('error', 'La base de datos aún no tiene las migraciones SaaS aplicadas.');
        }

        $user->update([
            'plan_actual' => $plan,
            'is_trial' => false,
            'trial_ends_at' => null,
        ]);

        if (Schema::hasTable('subscriptions')) {
            Subscription::create([
                'user_id' => $user->id,
                'plan' => $plan,
                'status' => 'active',
                'start_date' => now(),
            ]);
        }

        MetricsTracker::track('conversion', [
            'plan' => $plan,
            'provider' => 'manual_mvp',
        ]);

        return redirect()->route('saas.dashboard')->with('success', 'Plan actualizado. Stripe quedará conectado en la siguiente etapa.');
    }
}
