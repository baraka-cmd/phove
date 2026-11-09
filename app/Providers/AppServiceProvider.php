<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Partager les paramètres publics avec le layout et ses partials.
        View::composer('public.*', function ($view): void {
            try {
                $settings = Setting::allValues();
            } catch (Throwable $exception) {
                // Les vues publiques conservent leurs valeurs de secours si la table
                // settings n'est pas encore migrée ou temporairement indisponible.
                report($exception);
                $settings = [];
            }

            $view->with('siteSettings', $settings);
        });

        // Forcer HTTPS en production.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
