<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureSiteIsAvailable
{
    /**
     * Empêche l'accès public lorsque le mode maintenance est activé.
     * Les routes d'administration restent accessibles pour permettre la désactivation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*') || $request->is('up')) {
            return $next($request);
        }

        try {
            if ((string) Setting::get('maintenance_mode', '0') === '1') {
                $message = Setting::get(
                    'maintenance_message',
                    'Notre site est momentanément indisponible pour maintenance. Merci de revenir bientôt.'
                );

                return response()
                    ->view('public.maintenance', ['message' => $message], 503)
                    ->header('Retry-After', '3600');
            }
        } catch (Throwable $exception) {
            // Fail-open : une panne ou une table absente ne doit pas bloquer tout le site.
            report($exception);
        }

        return $next($request);
    }
}
