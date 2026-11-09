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
            $view->with('siteSettings', Setting::allValues());
        });

        // Forcer HTTPS en production.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
