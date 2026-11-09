<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forcer HTTPS en production.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Fournir les paramètres TiDB à toutes les vues publiques.
        // Lecture directe sans cache : les changements sont visibles dès la requête suivante.
        View::composer('public.*', function ($view): void {
            try {
                $view->with('siteSettings', Setting::query()->pluck('value', 'key')->all());
            } catch (Throwable $exception) {
                report($exception);
                $view->with('siteSettings', []);
            }
        });
    }
}