<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ─────────────────────────────────────────
        // ⭐ Trust proxies (Render, Vercel, etc.)
        // ─────────────────────────────────────────
        // Indispensable derrière un reverse proxy
        // pour détecter HTTPS correctement.
        $middleware->trustProxies(at: '*');

        // ─────────────────────────────────────────
        // Appliquer le mode maintenance aux routes web publiques.
        // Les routes /admin restent accessibles via le middleware lui-même.
        $middleware->web(append: [
            \App\Http\Middleware\EnsureSiteIsAvailable::class,
        ]);

        // Alias des middlewares personnalisés
        // ─────────────────────────────────────────
        $middleware->alias([
            'check.role'   => \App\Http\Middleware\CheckRole::class,
            'check.status' => \App\Http\Middleware\CheckStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();