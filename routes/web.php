<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Public\ProjetController;
use App\Http\Controllers\Public\ActualiteController;
use App\Http\Controllers\Public\DonController;
use App\Http\Controllers\Public\MembreController as PublicMembreController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\RechercheController;
use App\Http\Controllers\Admin\MembreController as AdminMembreController;
use App\Http\Controllers\Admin\CotisationController;


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
        Route::get('/', [AdminMembreController::class, 'index'])->name('index');
        Route::get('/create', [AdminMembreController::class, 'create'])->name('create');
        Route::post('/', [AdminMembreController::class, 'store'])->name('store');
        Route::get('/demandes', [AdminMembreController::class, 'demandes'])->name('demandes');  // ← Celle-ci
        Route::get('/{membre}', [AdminMembreController::class, 'show'])->name('show');
        Route::get('/{membre}/edit', [AdminMembreController::class, 'edit'])->name('edit');
        Route::put('/{membre}', [AdminMembreController::class, 'update'])->name('update');
        Route::delete('/{membre}', [AdminMembreController::class, 'destroy'])->name('destroy');
        Route::put('/{membre}/change-status', [AdminMembreController::class, 'changeStatus'])->name('changeStatus');
        Route::put('/{membre}/approuver', [AdminMembreController::class, 'approuver'])->name('approuver');
        Route::put('/{membre}/rejeter', [AdminMembreController::class, 'rejeter'])->name('rejeter');
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

    // Gestion des cotisations (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('cotisations')->name('cotisations.')->group(function () {
        Route::get('/', [CotisationController::class, 'index'])->name('index');
        Route::get('/create', [CotisationController::class, 'create'])->name('create');
        Route::post('/', [CotisationController::class, 'store'])->name('store');
        Route::get('/retard', [CotisationController::class, 'retard'])->name('retard');
        Route::get('/regle', [CotisationController::class, 'regle'])->name('regle');
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
Route::get('/actualites', [ActualiteController::class, 'index'])->name('actualites.index');
Route::get('/actualites/{slug}', [ActualiteController::class, 'show'])->name('actualites.show');

// Témoignages
Route::get('/temoignages', [ActualiteController::class, 'temoignages'])->name('temoignages');

// Transparence
Route::get('/transparence', [ActualiteController::class, 'transparence'])->name('transparence');
Route::get('/transparence/financiers', function () {
    return redirect()->route('transparence', ['tab' => 'financiers']);
})->name('transparence.financiers');
Route::get('/transparence/annuels', function () {
    return redirect()->route('transparence', ['tab' => 'annuels']);
})->name('transparence.annuels');
Route::get('/transparence/documents', function () {
    return redirect()->route('transparence', ['tab' => 'documents']);
})->name('transparence.documents');
// Impliquez-vous

Route::get('/devenir-benevole', function () { return 'Devenir bénévole'; })->name('devenir-benevole');
Route::get('/devenir-partenaire', function () { return 'Devenir partenaire'; })->name('devenir-partenaire');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

//Recherche
Route::get('/recherche', [RechercheController::class, 'index'])->name('recherche');



// Faire un don
Route::get('/faire-un-don', [DonController::class, 'index'])->name('don');
Route::post('/faire-un-don', [DonController::class, 'store'])->name('don.store');
Route::get('/don/merci', [DonController::class, 'merci'])->name('don.merci');

//Devenir membre
Route::get('/devenir-membre', [PublicMembreController::class, 'index'])->name('devenir-membre');
Route::post('/devenir-membre', [PublicMembreController::class, 'store'])->name('devenir-membre.store');
Route::get('/devenir-membre/confirmation', [PublicMembreController::class, 'confirmation'])->name('devenir-membre.confirmation');

// Devenir bénévole
Route::get('/devenir-benevole', function () {
    return view('public.devenir-benevole');
})->name('devenir-benevole');

Route::get('/devenir-benevole/confirmation', function () {
    return view('public.benevole-confirmation');
})->name('devenir-benevole.confirmation');

// Devenir partenaire
Route::get('/devenir-partenaire', function () {
    return view('public.devenir-partenaire');
})->name('devenir-partenaire');


Route::get('/devenir-partenaire/confirmation', function () {
    return view('public.partenaire-confirmation');
})->name('devenir-partenaire.confirmation');

// Newsletter
Route::post('/newsletter/subscribe', function () { return 'Abonnement newsletter'; })->name('newsletter.subscribe');