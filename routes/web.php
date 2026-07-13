<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Public\ProjetController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

// Accueil
Route::get('/', function () {
    return view('public.home');
})->name('home');

/*
|--------------------------------------------------------------------------
| ROUTES INSTITUTIONNELLES
|--------------------------------------------------------------------------
*/

// Qui sommes-nous ?
Route::get('/qui-sommes-nous', function () {
    return view('public.qui-sommes-nous');
})->name('qui-sommes-nous');

// Mission & Vision
Route::get('/mission-vision', function () {
    return view('public.mission-vision');
})->name('mission-vision');

// Nos Valeurs
Route::get('/nos-valeurs', function () {
    return view('public.nos-valeurs');
})->name('nos-valeurs');

// Gouvernance
Route::get('/gouvernance', function () {
    return view('public.gouvernance');
})->name('gouvernance');

// Organigramme
Route::get('/organigramme', function () {
    return view('public.organigramme');
})->name('organigramme');

// Mentions légales
Route::get('/mentions-legales', function () {
    return view('public.mentions-legales');
})->name('mentions-legales');

// Politique de confidentialité (redirige vers mentions légales pour l'instant)
Route::get('/politique-confidentialite', function () {
    return view('public.mentions-legales');
})->name('politique-confidentialite');

// Domaines d'intervention
Route::get('/domaines-intervention', function () {
    return view('public.domaines-intervention');
})->name('domaines-intervention');

// Partenaires
Route::get('/partenaires', function () {
    return view('public.partenaires');
})->name('partenaires');

/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

// Routes pour les visiteurs non connectés
Route::middleware('guest')->group(function () {

    // Connexion
    Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'login'])->name('login.post');

    // Mot de passe oublié
    Route::get('/admin/mot-de-passe-oublie', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/admin/mot-de-passe-oublie', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    // Réinitialisation du mot de passe
    Route::get('/admin/reinitialiser-mot-de-passe/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/admin/reinitialiser-mot-de-passe', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Connexion Google
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

// Déconnexion (accessible uniquement si connecté)
Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [LogoutController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| ROUTES DU DASHBOARD ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.status'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard (accessible à tous les rôles)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Gestion des utilisateurs (super_admin uniquement)
    Route::middleware('check.role:super_admin')->prefix('utilisateurs')->name('utilisateurs.')->group(function () {
        Route::get('/', function () { return 'Liste utilisateurs'; })->name('index');
    });

    // Gestion des projets (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('projets')->name('projets.')->group(function () {
        Route::get('/', function () { return 'Liste projets'; })->name('index');
    });

    // Gestion des membres (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('membres')->name('membres.')->group(function () {
        Route::get('/', function () { return 'Liste membres'; })->name('index');
    });

    // Gestion des articles (tous les rôles)
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', function () { return 'Liste articles'; })->name('index');
    });

    // Gestion des dons (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('dons')->name('dons.')->group(function () {
        Route::get('/', function () { return 'Liste dons'; })->name('index');
    });

    // Gestion des partenaires (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('partenaires')->name('partenaires.')->group(function () {
        Route::get('/', function () { return 'Liste partenaires'; })->name('index');
    });

    // Gestion des bénéficiaires (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('beneficiaires')->name('beneficiaires.')->group(function () {
        Route::get('/', function () { return 'Liste bénéficiaires'; })->name('index');
    });

    // Gestion des médias (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('medias')->name('medias.')->group(function () {
        Route::get('/', function () { return 'Liste médias'; })->name('index');
    });

    // Gestion financière (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('finances')->name('finances.')->group(function () {
        Route::get('/', function () { return 'Rapports financiers'; })->name('index');
    });

    // Gouvernance (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('gouvernance')->name('gouvernance.')->group(function () {
        Route::get('/', function () { return 'Gouvernance'; })->name('index');
    });

    // Messages de contact (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', function () { return 'Messages'; })->name('index');
    });

    // Paramètres (super_admin uniquement)
    Route::middleware('check.role:super_admin')->prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/', function () { return 'Paramètres'; })->name('index');
    });
});

/*
|--------------------------------------------------------------------------
| ROUTES DYNAMIQUES (PLACEHOLDERS POUR LE FUTUR)
|--------------------------------------------------------------------------
*/

// Projets (liste et détail)

Route::get('/projets', [ProjetController::class, 'index'])->name('projets.index');
Route::get('/projets/{slug}', [ProjetController::class, 'show'])->name('projets.show');

// Actualités
Route::get('/actualites', function () { return 'Actualités'; })->name('actualites.index');

// Transparence
Route::get('/transparence/rapports-financiers', function () { return 'Rapports financiers'; })->name('transparence.rapports-financiers');
Route::get('/transparence/rapports-annuels', function () { return 'Rapports annuels'; })->name('transparence.rapports-annuels');
Route::get('/transparence/documents-officiels', function () { return 'Documents officiels'; })->name('transparence.documents-officiels');

// Impliquez-vous
Route::get('/faire-un-don', function () { return 'Faire un don'; })->name('don');
Route::get('/devenir-membre', function () { return 'Devenir membre'; })->name('devenir-membre');
Route::get('/devenir-benevole', function () { return 'Devenir bénévole'; })->name('devenir-benevole');
Route::get('/devenir-partenaire', function () { return 'Devenir partenaire'; })->name('devenir-partenaire');

// Contact
Route::get('/contact', function () { return 'Contact'; })->name('contact');

// Recherche
Route::get('/recherche', function () { return 'Recherche'; })->name('recherche');

// Newsletter
Route::post('/newsletter/subscribe', function () { return 'Abonnement newsletter'; })->name('newsletter.subscribe');