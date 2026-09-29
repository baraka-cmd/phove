<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS PUBLICS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Public\ProjetController as PublicProjetController;
use App\Http\Controllers\Public\ActualiteController;
use App\Http\Controllers\Public\DonController;
use App\Http\Controllers\Public\MembreController as PublicMembreController;
use App\Http\Controllers\Public\ContactController as PublicContactController;
use App\Http\Controllers\Public\RechercheController;
use App\Http\Controllers\Public\GouvernanceController;
use App\Http\Controllers\Public\OrganigrammeController;

/*
|--------------------------------------------------------------------------
| CONTROLLERS AUTH
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| CONTROLLERS ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\MembreController as AdminMembreController;
use App\Http\Controllers\Admin\CotisationController;
use App\Http\Controllers\Admin\GouvernanceController as AdminGouvernanceController;
use App\Http\Controllers\Admin\ProjetController as AdminProjetController;
use App\Http\Controllers\Admin\IndicateurController;
use App\Http\Controllers\Admin\DonController as AdminDonController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PartenaireController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\TemoignageController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StructureTerritorialeController;
use App\Http\Controllers\Admin\DossierPersonnelController;
use App\Http\Controllers\Admin\ControleController;
use App\Http\Controllers\Admin\SalaireController;
use App\Http\Controllers\Admin\PaiementSalaireController;


use App\Models\Article;
use App\Models\Projet;

/*
|--------------------------------------------------------------------------
| CONTROLLERS ADMIN — MODULE STOCK
|--------------------------------------------------------------------------
|
| Ces imports sont déclarés ici car le bloc STOCKS sera livré
| dans la seconde partie. Ils doivent exister en haut du fichier.
|
*/

use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\EmplacementStockController;
use App\Http\Controllers\Admin\EntreeStockController;
use App\Http\Controllers\Admin\BonSortieController;
use App\Http\Controllers\Admin\ReservationStockController;
use App\Http\Controllers\Admin\LotStockController;
use App\Http\Controllers\Admin\InventaireStockController;
use App\Http\Controllers\Admin\MouvementStockController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Public\HomeController;


use App\Http\Controllers\Public\GalerieController;

use App\Http\Controllers\Public\TemoignageController as PublicTemoignageController;


/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/





Route::get('/', [HomeController::class, 'index'])->name('home');




/*
|--------------------------------------------------------------------------
| Route pour servir les médias (contourne le 403 de php artisan serve)
|--------------------------------------------------------------------------
*/

Route::get('/media/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    // Vérifier que le fichier existe
    if (!file_exists($fullPath)) {
        abort(404, 'Fichier introuvable');
    }

    // Vérifier que le chemin est bien dans storage/app/public (sécurité)
    $realPath = realpath($fullPath);
    $basePath = realpath(storage_path('app/public'));

    if (strpos($realPath, $basePath) !== 0) {
        abort(403, 'Accès interdit');
    }

    // Retourner le fichier
    return response()->file($fullPath, [
        'Content-Type' => mime_content_type($fullPath),
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('media.show');

/*
|--------------------------------------------------------------------------
| ROUTES INSTITUTIONNELLES
|--------------------------------------------------------------------------
*/

Route::get('/qui-sommes-nous', function () {
    return view('public.qui-sommes-nous');
})->name('qui-sommes-nous');

Route::get('/mission-vision', function () {
    return view('public.mission-vision');
})->name('mission-vision');

Route::get('/nos-valeurs', function () {
    return view('public.nos-valeurs');
})->name('nos-valeurs');

Route::get('/domaines-intervention', function () {
    return view('public.domaines-intervention');
})->name('domaines-intervention');

Route::get('/mentions-legales', function () {
    return view('public.mentions-legales');
})->name('mentions-legales');

Route::get('/politique-confidentialite', function () {
    return view('public.mentions-legales');
})->name('politique-confidentialite');

Route::get('/partenaires', [
    App\Http\Controllers\Public\PartenaireController::class,
    'index'
])->name('partenaires');

Route::get('/gouvernance', [
    GouvernanceController::class,
    'index'
])->name('gouvernance');

Route::get('/organigramme', [
    OrganigrammeController::class,
    'index'
])->name('organigramme');

// ═══════════════════════════════════════════════
// GALERIE MÉDIA (AJOUTER ICI)
// ═══════════════════════════════════════════════
// Galerie média
Route::get('/galerie', [
    GalerieController::class,
    'index'
])->name('galerie.index');

// Témoignages publics
Route::get('/temoignages', [
    PublicTemoignageController::class,
    'index'
])->name('temoignages.index');

/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/admin/login', [
        LoginController::class,
        'showLoginForm'
    ])->name('login');

    Route::post('/admin/login', [
        LoginController::class,
        'login'
    ])->name('login.post');

    Route::get('/admin/mot-de-passe-oublie', [
        ForgotPasswordController::class,
        'showLinkRequestForm'
    ])->name('password.request');

    Route::post('/admin/mot-de-passe-oublie', [
        ForgotPasswordController::class,
        'sendResetLinkEmail'
    ])->name('password.email');

    Route::get('/admin/reinitialiser-mot-de-passe/{token}', [
        ResetPasswordController::class,
        'showResetForm'
    ])->name('password.reset');

    Route::post('/admin/reinitialiser-mot-de-passe', [
        ResetPasswordController::class,
        'reset'
    ])->name('password.update');

    Route::get('/auth/google', [
        GoogleAuthController::class,
        'redirectToGoogle'
    ])->name('google.login');

    Route::get('/auth/google/callback', [
        GoogleAuthController::class,
        'handleGoogleCallback'
    ])->name('google.callback');
});


/*
|--------------------------------------------------------------------------
| DÉCONNEXION
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/admin/logout', [
        LogoutController::class,
        'logout'
    ])->name('logout');
});


/*
|--------------------------------------------------------------------------
| ROUTES DU DASHBOARD ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'check.status'
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | DASHBOARD
        |----------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');


        /*
        |----------------------------------------------------------------------
        | UTILISATEURS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:super_admin')
            ->prefix('utilisateurs')
            ->name('users.')
            ->group(function () {

                Route::get('/', [
                    UserController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    UserController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    UserController::class,
                    'store'
                ])->name('store');

                Route::get('/{user}', [
                    UserController::class,
                    'show'
                ])->name('show');

                Route::get('/{user}/edit', [
                    UserController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{user}', [
                    UserController::class,
                    'update'
                ])->name('update');

                Route::delete('/{user}', [
                    UserController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{user}/activer', [
                    UserController::class,
                    'activer'
                ])->name('activer');

                Route::put('/{user}/desactiver', [
                    UserController::class,
                    'desactiver'
                ])->name('desactiver');

                Route::put('/{user}/suspendre', [
                    UserController::class,
                    'suspendre'
                ])->name('suspendre');

                Route::get('/{user}/password', [
                    UserController::class,
                    'editPassword'
                ])->name('password');

                Route::put('/{user}/password', [
                    UserController::class,
                    'updatePassword'
                ])->name('password.update');
            });


        /*
        |----------------------------------------------------------------------
        | PROJETS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('projets')
            ->name('projets.')
            ->group(function () {

                Route::get('/', [
                    AdminProjetController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    AdminProjetController::class,
                    'create'
                ])->name('create');

                Route::get('/budgets', [
                    AdminProjetController::class,
                    'budgets'
                ])->name('budgets');

                Route::post('/', [
                    AdminProjetController::class,
                    'store'
                ])->name('store');

                Route::get('/{projet}', [
                    AdminProjetController::class,
                    'show'
                ])->name('show');

                Route::get('/{projet}/edit', [
                    AdminProjetController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{projet}', [
                    AdminProjetController::class,
                    'update'
                ])->name('update');

                Route::delete('/{projet}', [
                    AdminProjetController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{projet}/change-status', [
                    AdminProjetController::class,
                    'changeStatus'
                ])->name('changeStatus');

                Route::put('/{projet}/toggle-publier', [
                    AdminProjetController::class,
                    'togglePublier'
                ])->name('togglePublier');

                Route::put('/{projet}/budget', [
                    AdminProjetController::class,
                    'updateBudget'
                ])->name('updateBudget');

                /*
                 * Indicateurs
                 */
                Route::get('/{projet}/indicateurs', [
                    IndicateurController::class,
                    'index'
                ])->name('indicateurs.index');

                Route::post('/{projet}/indicateurs', [
                    IndicateurController::class,
                    'store'
                ])->name('indicateurs.store');

                Route::put('/{projet}/indicateurs/{indicateur}', [
                    IndicateurController::class,
                    'update'
                ])->name('indicateurs.update');

                Route::put('/{projet}/indicateurs/{indicateur}/value', [
                    IndicateurController::class,
                    'updateValue'
                ])->name('indicateurs.updateValue');

                Route::delete('/{projet}/indicateurs/{indicateur}', [
                    IndicateurController::class,
                    'destroy'
                ])->name('indicateurs.destroy');
            });


        /*
        |----------------------------------------------------------------------
        | MEMBRES
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('membres')
            ->name('membres.')
            ->group(function () {

                Route::get('/', [
                    AdminMembreController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    AdminMembreController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    AdminMembreController::class,
                    'store'
                ])->name('store');

                Route::get('/demandes', [
                    AdminMembreController::class,
                    'demandes'
                ])->name('demandes');

                Route::get('/sanctions', [
                    AdminMembreController::class,
                    'sanctions'
                ])->name('sanctions');

                Route::get('/{membre}', [
                    AdminMembreController::class,
                    'show'
                ])->name('show');

                Route::get('/{membre}/edit', [
                    AdminMembreController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{membre}', [
                    AdminMembreController::class,
                    'update'
                ])->name('update');

                Route::delete('/{membre}', [
                    AdminMembreController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{membre}/change-status', [
                    AdminMembreController::class,
                    'changeStatus'
                ])->name('changeStatus');

                Route::put('/{membre}/approuver', [
                    AdminMembreController::class,
                    'approuver'
                ])->name('approuver');

                Route::put('/{membre}/rejeter', [
                    AdminMembreController::class,
                    'rejeter'
                ])->name('rejeter');

                Route::put('/{membre}/reintegrer', [
                    AdminMembreController::class,
                    'reintegrer'
                ])->name('reintegrer');

                Route::put('/{membre}/suspendre', [
                    AdminMembreController::class,
                    'suspendre'
                ])->name('suspendre');

                Route::put('/{membre}/exclure', [
                    AdminMembreController::class,
                    'exclure'
                ])->name('exclure');
            });


        /*
        |----------------------------------------------------------------------
        | ARTICLES
        |----------------------------------------------------------------------
        */

        Route::prefix('articles')
            ->name('articles.')
            ->group(function () {

                Route::get('/', [
                    ArticleController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    ArticleController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    ArticleController::class,
                    'store'
                ])->name('store');

                Route::get('/{article}', [
                    ArticleController::class,
                    'show'
                ])->name('show');

                Route::get('/{article}/edit', [
                    ArticleController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{article}', [
                    ArticleController::class,
                    'update'
                ])->name('update');

                Route::delete('/{article}', [
                    ArticleController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{article}/toggle-publier', [
                    ArticleController::class,
                    'togglePublier'
                ])->name('togglePublier');

                Route::put('/{article}/toggle-epingler', [
                    ArticleController::class,
                    'toggleEpingler'
                ])->name('toggleEpingler');
            });


        /*
        |----------------------------------------------------------------------
        | DONS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('dons')
            ->name('dons.')
            ->group(function () {

                Route::get('/', [
                    AdminDonController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    AdminDonController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    AdminDonController::class,
                    'store'
                ])->name('store');

                Route::get('/{don}/telecharger-recus', [
                    AdminDonController::class,
                    'telechargerRecu'
                ])->name('telechargerRecu');

                Route::get('/{don}/recu', [
                    AdminDonController::class,
                    'voirRecu'
                ])->name('recu');

                Route::get('/{don}', [
                    AdminDonController::class,
                    'show'
                ])->name('show');

                Route::get('/{don}/edit', [
                    AdminDonController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{don}', [
                    AdminDonController::class,
                    'update'
                ])->name('update');

                Route::delete('/{don}', [
                    AdminDonController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{don}/generer-recu', [
                    AdminDonController::class,
                    'genererRecu'
                ])->name('genererRecu');

                Route::put('/{don}/change-status', [
                    AdminDonController::class,
                    'changeStatus'
                ])->name('changeStatus');
            });


        /*
        |----------------------------------------------------------------------
        | PARTENAIRES
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('partenaires')
            ->name('partenaires.')
            ->group(function () {

                Route::get('/', [
                    PartenaireController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    PartenaireController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    PartenaireController::class,
                    'store'
                ])->name('store');

                Route::get('/{partenaire}', [
                    PartenaireController::class,
                    'show'
                ])->name('show');

                Route::get('/{partenaire}/edit', [
                    PartenaireController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{partenaire}', [
                    PartenaireController::class,
                    'update'
                ])->name('update');

                Route::delete('/{partenaire}', [
                    PartenaireController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | TÉMOIGNAGES
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('temoignages')
            ->name('temoignages.')
            ->group(function () {

                Route::get('/', [
                    TemoignageController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    TemoignageController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    TemoignageController::class,
                    'store'
                ])->name('store');

                Route::get('/{temoignage}', [
                    TemoignageController::class,
                    'show'
                ])->name('show');

                Route::get('/{temoignage}/edit', [
                    TemoignageController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{temoignage}', [
                    TemoignageController::class,
                    'update'
                ])->name('update');

                Route::delete('/{temoignage}', [
                    TemoignageController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{temoignage}/approuver', [
                    TemoignageController::class,
                    'approuver'
                ])->name('approuver');

                Route::put('/{temoignage}/rejeter', [
                    TemoignageController::class,
                    'rejeter'
                ])->name('rejeter');

                Route::put('/{temoignage}/archiver', [
                    TemoignageController::class,
                    'archiver'
                ])->name('archiver');

                Route::put('/{temoignage}/desarchiver', [
                    TemoignageController::class,
                    'desarchiver'
                ])->name('desarchiver');
            });


        /*
        |----------------------------------------------------------------------
        | MÉDIAS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('medias')
            ->name('medias.')
            ->group(function () {

                Route::get('/', [
                    MediaController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    MediaController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    MediaController::class,
                    'store'
                ])->name('store');

                Route::get('/{media}', [
                    MediaController::class,
                    'show'
                ])->name('show');

                Route::get('/{media}/edit', [
                    MediaController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{media}', [
                    MediaController::class,
                    'update'
                ])->name('update');

                Route::delete('/{media}', [
                    MediaController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{media}/toggle-publier', [
                    MediaController::class,
                    'togglePublier'
                ])->name('togglePublier');

                Route::put('/{media}/replace-file', [
                    MediaController::class,
                    'replaceFile'
                ])->name('replaceFile');

                Route::post('/{media}/incrementer-vues', [
                    MediaController::class,
                    'incrementerVues'
                ])->name('incrementerVues');

                /*
                 * Actions batch
                 */
                Route::delete('/batch/delete', [
                    MediaController::class,
                    'batchDelete'
                ])->name('batchDelete');

                Route::put('/batch/change-album', [
                    MediaController::class,
                    'batchChangeAlbum'
                ])->name('batchChangeAlbum');

                Route::put('/batch/toggle-publier', [
                    MediaController::class,
                    'batchTogglePublier'
                ])->name('batchTogglePublier');
            });


        /*
        |----------------------------------------------------------------------
        | FINANCES
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('finances')
            ->name('finances.')
            ->group(function () {

                Route::get('/', [
                    FinanceController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    FinanceController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    FinanceController::class,
                    'store'
                ])->name('store');

                Route::get('/{rapport}', [
                    FinanceController::class,
                    'show'
                ])->name('show');

                Route::put('/{rapport}/publier', [
                    FinanceController::class,
                    'publier'
                ])->name('publier');

                Route::get('/{rapport}/pdf', [
                    FinanceController::class,
                    'telechargerPdf'
                ])->name('telechargerPdf');

                Route::delete('/{rapport}', [
                    FinanceController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | GOUVERNANCE
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('gouvernance')
            ->name('gouvernance.')
            ->group(function () {

                Route::get('/', [
                    AdminGouvernanceController::class,
                    'index'
                ])->name('index');

                Route::get('/organes/{organe}', [
                    AdminGouvernanceController::class,
                    'organe'
                ])->name('organe');

                Route::post('/organes/store', [
                    AdminGouvernanceController::class,
                    'storeMembre'
                ])->name('storeMembre');

                Route::put('/organes/{organe}/update', [
                    AdminGouvernanceController::class,
                    'updateMembre'
                ])->name('updateMembre');

                Route::put('/organes/{organe}/retirer', [
                    AdminGouvernanceController::class,
                    'retirerMembre'
                ])->name('retirerMembre');

                /*
                 * Assemblées générales
                 */
                Route::get('/assemblies', [
                    AdminGouvernanceController::class,
                    'assemblies'
                ])->name('assemblies');

                Route::get('/assemblies/create', [
                    AdminGouvernanceController::class,
                    'createAssembly'
                ])->name('assemblies.create');

                Route::post('/assemblies', [
                    AdminGouvernanceController::class,
                    'storeAssembly'
                ])->name('assemblies.store');

                Route::get('/assemblies/{assembly}', [
                    AdminGouvernanceController::class,
                    'showAssembly'
                ])->name('assemblies.show');

                Route::get('/assemblies/{assembly}/edit', [
                    AdminGouvernanceController::class,
                    'editAssembly'
                ])->name('assemblies.edit');

                Route::put('/assemblies/{assembly}', [
                    AdminGouvernanceController::class,
                    'updateAssembly'
                ])->name('assemblies.update');

                Route::delete('/assemblies/{assembly}', [
                    AdminGouvernanceController::class,
                    'destroyAssembly'
                ])->name('assemblies.destroy');
            });


        /*
        |----------------------------------------------------------------------
        | CONTACTS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('contacts')
            ->name('contacts.')
            ->group(function () {

                Route::get('/', [
                    AdminContactController::class,
                    'index'
                ])->name('index');

                Route::get('/{email}', [
                    AdminContactController::class,
                    'show'
                ])->name('show');

                Route::put('/{contact}/lu', [
                    AdminContactController::class,
                    'marquerLu'
                ])->name('marquerLu');

                Route::put('/{contact}/non-lu', [
                    AdminContactController::class,
                    'marquerNonLu'
                ])->name('marquerNonLu');

                Route::put('/{contact}/statut', [
                    AdminContactController::class,
                    'changeStatut'
                ])->name('changeStatut');

                Route::put('/{contact}/archiver', [
                    AdminContactController::class,
                    'archiver'
                ])->name('archiver');

                Route::put('/{contact}/desarchiver', [
                    AdminContactController::class,
                    'desarchiver'
                ])->name('desarchiver');

                Route::put('/{contact}/note', [
                    AdminContactController::class,
                    'note'
                ])->name('note');

                Route::post('/{contact}/repondre', [
                    AdminContactController::class,
                    'repondre'
                ])->name('repondre');

                Route::put('/{contact}/assigner', [
                    AdminContactController::class,
                    'assigner'
                ])->name('assigner');

                Route::delete('/conversation/supprimer', [
                    AdminContactController::class,
                    'destroyConversation'
                ])->name('destroyConversation');

                Route::delete('/{contact}', [
                    AdminContactController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | MESSAGERIE INTERNE
        |----------------------------------------------------------------------
        */

        Route::prefix('messages')
            ->name('messages.')
            ->group(function () {

                Route::get('/', [
                    MessageController::class,
                    'index'
                ])->name('index');

                Route::get('/envoyes', [
                    MessageController::class,
                    'envoyes'
                ])->name('envoyes');

                Route::get('/composer', [
                    MessageController::class,
                    'composer'
                ])->name('composer');

                Route::post('/envoyer', [
                    MessageController::class,
                    'envoyer'
                ])->name('envoyer');

                Route::get('/{user}', [
                    MessageController::class,
                    'show'
                ])->name('show');

                Route::post('/{user}/repondre', [
                    MessageController::class,
                    'repondre'
                ])->name('repondre');

                Route::put('/{message}/lu', [
                    MessageController::class,
                    'marquerLu'
                ])->name('marquerLu');

                Route::put('/{user}/archiver', [
                    MessageController::class,
                    'archiverConversation'
                ])->name('archiverConversation');

                Route::delete('/{message}', [
                    MessageController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | PARAMÈTRES
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:super_admin')
            ->prefix('parametres')
            ->name('parametres.')
            ->group(function () {

                Route::get('/', [
                    SettingController::class,
                    'index'
                ])->name('index');

                Route::put('/update', [
                    SettingController::class,
                    'update'
                ])->name('update');

                Route::post('/remove-image', [
                    SettingController::class,
                    'removeImage'
                ])->name('removeImage');
            });


        /*
        |----------------------------------------------------------------------
        | COTISATIONS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('cotisations')
            ->name('cotisations.')
            ->group(function () {

                Route::get('/', [
                    CotisationController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    CotisationController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    CotisationController::class,
                    'store'
                ])->name('store');

                Route::get('/retard', [
                    CotisationController::class,
                    'retard'
                ])->name('retard');

                Route::get('/regle', [
                    CotisationController::class,
                    'regle'
                ])->name('regle');
            });


        /*
        |----------------------------------------------------------------------
        | DOCUMENTS
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('documents')
            ->name('documents.')
            ->group(function () {

                Route::get('/', [
                    DocumentController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    DocumentController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    DocumentController::class,
                    'store'
                ])->name('store');

                Route::get('/{document}', [
                    DocumentController::class,
                    'show'
                ])->name('show');

                Route::get('/{document}/edit', [
                    DocumentController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{document}', [
                    DocumentController::class,
                    'update'
                ])->name('update');

                Route::delete('/{document}', [
                    DocumentController::class,
                    'destroy'
                ])->name('destroy');

                Route::put('/{document}/replace-file', [
                    DocumentController::class,
                    'replaceFile'
                ])->name('replaceFile');

                Route::put('/{document}/toggle-public', [
                    DocumentController::class,
                    'togglePublic'
                ])->name('togglePublic');

                Route::get('/{document}/download', [
                    DocumentController::class,
                    'download'
                ])->name('download');

                Route::get('/{document}/preview', [
                    DocumentController::class,
                    'preview'
                ])->name('preview');
            });
                    /*
        |----------------------------------------------------------------------
        | STRUCTURES TERRITORIALES
        |----------------------------------------------------------------------
        */

        Route::prefix('structures')
            ->name('structures.')
            ->group(function () {

                Route::get('/', [
                    StructureTerritorialeController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    StructureTerritorialeController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    StructureTerritorialeController::class,
                    'store'
                ])->name('store');

                Route::get('/{structure}', [
                    StructureTerritorialeController::class,
                    'show'
                ])->name('show');

                Route::get('/{structure}/edit', [
                    StructureTerritorialeController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{structure}', [
                    StructureTerritorialeController::class,
                    'update'
                ])->name('update');

                Route::delete('/{structure}', [
                    StructureTerritorialeController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | DOSSIERS DU PERSONNEL
        |----------------------------------------------------------------------
        */

        Route::prefix('dossiers')
            ->name('dossiers.')
            ->group(function () {

                Route::get('/', [
                    DossierPersonnelController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    DossierPersonnelController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    DossierPersonnelController::class,
                    'store'
                ])->name('store');

                Route::get('/{dossier}', [
                    DossierPersonnelController::class,
                    'show'
                ])->name('show');

                Route::post('/{dossier}/documents/{document}/upload', [
                    DossierPersonnelController::class,
                    'uploadDocument'
                ])->name('documents.upload');

                Route::get('/{dossier}/edit', [
                    DossierPersonnelController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{dossier}', [
                    DossierPersonnelController::class,
                    'update'
                ])->name('update');

                Route::delete('/documents/{document}', [
                    DossierPersonnelController::class,
                    'destroyDocument'
                ])->name('documents.destroy');

                Route::put('/{dossier}/valider', [
                    DossierPersonnelController::class,
                    'valider'
                ])->name('valider');

                Route::get('/documents/{document}/voir', [
                    DossierPersonnelController::class,
                    'voirDocument'
                ])->name('documents.voir');

                Route::delete('/{dossier}', [
                    DossierPersonnelController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | STOCKS
        |----------------------------------------------------------------------
        |
        | Architecture du module Stock :
        |
        |   Stocks
        |   ├── Articles
        |   ├── Emplacements
        |   ├── Entrées
        |   ├── Bons de sortie
        |   ├── Réservations
        |   ├── Lots & péremption
        |   ├── Inventaires
        |   └── Mouvements
        |
        | Le StockController ne gère QUE le catalogue des articles.
        | Chaque opération métier possède son propre contrôleur.
        |
        | IMPORTANT :
        | Toutes les routes spécifiques (/lots/..., /entrees/...,
        | /sorties/..., etc.) doivent être déclarées AVANT les
        | routes génériques (/{stock}).
        |
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('stocks')
            ->name('stocks.')
            ->group(function () {

                /*
                |----------------------------------------------------------
                | ARTICLES / STOCKS (CATALOGUE)
                |----------------------------------------------------------
                */

                Route::get('/', [
                    StockController::class,
                    'index'
                ])->name('index');

                /*
                * Vue d'ensemble du module Stock.
                *
                * GET /admin/stocks/overview
                */
                Route::get('/overview', [
                    StockController::class,
                    'overview'
                ])->name('overview');

                Route::get('/create', [
                    StockController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    StockController::class,
                    'store'
                ])->name('store');


                /*
                |----------------------------------------------------------
                | EMPLACEMENTS
                |----------------------------------------------------------
                |
                | Le référentiel des emplacements physiques.
                | Déclaré AVANT /{stock}.
                |
                */

                Route::prefix('emplacements')
                    ->name('emplacements.')
                    ->group(function () {

                        Route::get('/', [
                            EmplacementStockController::class,
                            'index'
                        ])->name('index');

                        Route::get('/create', [
                            EmplacementStockController::class,
                            'create'
                        ])->name('create');

                        Route::post('/', [
                            EmplacementStockController::class,
                            'store'
                        ])->name('store');

                        Route::get('/{emplacement}', [
                            EmplacementStockController::class,
                            'show'
                        ])->name('show');

                        Route::get('/{emplacement}/edit', [
                            EmplacementStockController::class,
                            'edit'
                        ])->name('edit');

                        Route::put('/{emplacement}', [
                            EmplacementStockController::class,
                            'update'
                        ])->name('update');

                        Route::delete('/{emplacement}', [
                            EmplacementStockController::class,
                            'destroy'
                        ])->name('destroy');

                        Route::post('/{emplacement}/activer', [
                            EmplacementStockController::class,
                            'activer'
                        ])->name('activer');

                        Route::post('/{emplacement}/desactiver', [
                            EmplacementStockController::class,
                            'desactiver'
                        ])->name('desactiver');
                    });


                /*
                |----------------------------------------------------------
                | ENTRÉES DE STOCK
                |----------------------------------------------------------
                |
                | Une entrée crée ou alimente un lot + crée un mouvement.
                |
                */

                Route::get('/entrees', [
                    EntreeStockController::class,
                    'index'
                ])->name('entrees.index');

                Route::get('/entrees/create', [
                    EntreeStockController::class,
                    'create'
                ])->name('entrees.create');

                Route::post('/entrees', [
                    EntreeStockController::class,
                    'store'
                ])->name('entrees.store');

                Route::get('/entrees/{entree}', [
                    EntreeStockController::class,
                    'show'
                ])->name('entrees.show');


                /*
                |----------------------------------------------------------
                | BONS DE SORTIE
                |----------------------------------------------------------
                |
                | Workflow :
                |
                |   brouillon
                |       ↓ soumettre
                |   soumis
                |       ↓ valider
                |   valide
                |       ↓ préparer
                |   en_preparation
                |       ↓ sortir
                |   sorti
                |
                | Depuis brouillon, soumis ou valide : annuler.
                |
                */

                Route::get('/sorties', [
                    BonSortieController::class,
                    'index'
                ])->name('sorties.index');

                Route::get('/sorties/create', [
                    BonSortieController::class,
                    'create'
                ])->name('sorties.create');

                Route::post('/sorties', [
                    BonSortieController::class,
                    'store'
                ])->name('sorties.store');

                Route::get('/sorties/{bonSortie}', [
                    BonSortieController::class,
                    'show'
                ])->name('sorties.show');

                /*
                 * Transitions du workflow.
                 */
                Route::post('/sorties/{bonSortie}/soumettre', [
                    BonSortieController::class,
                    'soumettre'
                ])->name('sorties.soumettre');

                Route::post('/sorties/{bonSortie}/valider', [
                    BonSortieController::class,
                    'valider'
                ])->name('sorties.valider');

                /*
                 * Écran de validation (GET).
                 */
                Route::get('/sorties/{bonSortie}/validation', [
                    BonSortieController::class,
                    'validation'
                ])->name('sorties.validation');

                /*
                 * Écran de préparation (GET).
                 *
                 * NB : il existe aussi une action POST
                 * /sorties/{bonSortie}/preparation qui
                 * déclenche le passage en préparation.
                 * Les deux routes partagent la même URL,
                 * différenciées par la méthode HTTP.
                 */
                Route::get('/sorties/{bonSortie}/preparation', [
                    BonSortieController::class,
                    'preparationEcran'
                ])->name('sorties.preparation');

                Route::post('/sorties/{bonSortie}/preparation', [
                    BonSortieController::class,
                    'preparation'
                ])->name('sorties.preparation.post');

                /*
                 * Exécution physique.
                 */
                Route::post('/sorties/{bonSortie}/sortir', [
                    BonSortieController::class,
                    'sortir'
                ])->name('sorties.sortir');

                /*
                 * Annulation.
                 */
                Route::post('/sorties/{bonSortie}/annuler', [
                    BonSortieController::class,
                    'annuler'
                ])->name('sorties.annuler');


                /*
                |----------------------------------------------------------
                | RÉSERVATIONS
                |----------------------------------------------------------
                */

                Route::get('/reservations', [
                    ReservationStockController::class,
                    'index'
                ])->name('reservations.index');

                Route::get('/reservations/create', [
                    ReservationStockController::class,
                    'create'
                ])->name('reservations.create');

                Route::post('/reservations', [
                    ReservationStockController::class,
                    'store'
                ])->name('reservations.store');

                Route::get('/reservations/{reservation}', [
                    ReservationStockController::class,
                    'show'
                ])->name('reservations.show');

                Route::post('/reservations/{reservation}/confirmer', [
                    ReservationStockController::class,
                    'confirmer'
                ])->name('reservations.confirmer');

                Route::post('/reservations/{reservation}/annuler', [
                    ReservationStockController::class,
                    'annuler'
                ])->name('reservations.annuler');


                /*
                |----------------------------------------------------------
                | LOTS
                |----------------------------------------------------------
                |
                | IMPORTANT :
                | /lots/expiration DOIT être déclarée AVANT /lots/{lot}.
                |
                */

                Route::get('/lots', [
                    LotStockController::class,
                    'index'
                ])->name('lots.index');

                /*
                 * Écran de surveillance des expirations (GET).
                 */
                Route::get('/lots/expiration', [
                    LotStockController::class,
                    'expiration'
                ])->name('lots.expiration');

                /*
                 * Traitement des expirations (POST).
                 */
                Route::post('/lots/expiration', [
                    LotStockController::class,
                    'traiterExpirations'
                ])->name('lots.traiterExpirations');

                /*
                 * Détail d'un lot.
                 */
                Route::get('/lots/{lot}', [
                    LotStockController::class,
                    'show'
                ])->name('lots.show');

                /*
                 * Blocage / déblocage.
                 */
                Route::post('/lots/{lot}/bloquer', [
                    LotStockController::class,
                    'bloquer'
                ])->name('lots.bloquer');

                Route::post('/lots/{lot}/debloquer', [
                    LotStockController::class,
                    'debloquer'
                ])->name('lots.debloquer');


                /*
                |----------------------------------------------------------
                | INVENTAIRES
                |----------------------------------------------------------
                */

                Route::get('/inventaires', [
                    InventaireStockController::class,
                    'index'
                ])->name('inventaires.index');

                Route::get('/inventaires/create', [
                    InventaireStockController::class,
                    'create'
                ])->name('inventaires.create');

                Route::post('/inventaires', [
                    InventaireStockController::class,
                    'store'
                ])->name('inventaires.store');

                Route::get('/inventaires/{inventaire}', [
                    InventaireStockController::class,
                    'show'
                ])->name('inventaires.show');

                /*
                 * Comptage.
                 */
                Route::post('/inventaires/{inventaire}/compter', [
                    InventaireStockController::class,
                    'compter'
                ])->name('inventaires.compter');

                /*
                 * Validation (déclenche les ajustements).
                 */
                Route::post('/inventaires/{inventaire}/valider', [
                    InventaireStockController::class,
                    'valider'
                ])->name('inventaires.valider');

                /*
                 * Annulation.
                 */
                Route::post('/inventaires/{inventaire}/annuler', [
                    InventaireStockController::class,
                    'annuler'
                ])->name('inventaires.annuler');


                /*
                |----------------------------------------------------------
                | MOUVEMENTS (LECTURE SEULE)
                |----------------------------------------------------------
                |
                | Les mouvements sont un JOURNAL IMMUABLE.
                | Aucune route store/update/delete.
                |
                */

                Route::get('/mouvements', [
                    MouvementStockController::class,
                    'index'
                ])->name('mouvements.index');

                Route::get('/mouvements/{mouvement}', [
                    MouvementStockController::class,
                    'show'
                ])->name('mouvements.show');


                /*
                |----------------------------------------------------------
                | ARTICLE INDIVIDUEL (ROUTES GÉNÉRIQUES)
                |----------------------------------------------------------
                |
                | IMPORTANT :
                | Ces routes génériques /{stock} DOIVENT être
                | déclarées APRÈS toutes les routes spécifiques
                | (/lots, /entrees, /sorties, /reservations,
                | /inventaires, /mouvements, /emplacements).
                |
                */

                Route::get('/{stock}', [
                    StockController::class,
                    'show'
                ])->name('show');

                Route::get('/{stock}/edit', [
                    StockController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{stock}', [
                    StockController::class,
                    'update'
                ])->name('update');

                Route::patch('/{stock}/archive', [
                    StockController::class,
                    'archive'
                ])->name('archive');
            });


        /*
        |----------------------------------------------------------------------
        | SALAIRES
        |----------------------------------------------------------------------
        |
        | Cycle :
        |
        |   Création
        |       ↓
        |   Brouillon
        |       ↓
        |   Validation
        |       ↓
        |   Validé / À payer
        |       ↓
        |   Paiement
        |       ↓
        |   Partiellement payé
        |       ↓
        |   Payé
        |
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('salaires')
            ->name('salaires.')
            ->group(function () {

                /*
                 * Grille salariale : DOIT être avant /{salaire}.
                 */
                Route::get('/grille', [
                    SalaireController::class,
                    'grille'
                ])->name('grille');

                Route::get('/', [
                    SalaireController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    SalaireController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    SalaireController::class,
                    'store'
                ])->name('store');

                /*
                 * Paiements d'un salaire.
                 */
                Route::get('/{salaire}/paiements', [
                    PaiementSalaireController::class,
                    'paiementsDuSalaire'
                ])->name('paiements.index');

                Route::get('/{salaire}/paiements/create', [
                    PaiementSalaireController::class,
                    'create'
                ])->name('paiements.create');

                Route::post('/{salaire}/paiements', [
                    PaiementSalaireController::class,
                    'store'
                ])->name('paiements.store');

                /*
                 * Bulletin.
                 */
                Route::get('/{salaire}/bulletin', [
                    SalaireController::class,
                    'bulletin'
                ])->name('bulletin');

                /*
                 * Modification.
                 */
                Route::get('/{salaire}/edit', [
                    SalaireController::class,
                    'edit'
                ])->name('edit');

                Route::match(
                    ['put', 'patch'],
                    '/{salaire}',
                    [SalaireController::class, 'update']
                )->name('update');

                /*
                 * Validation.
                 */
                Route::put('/{salaire}/valider', [
                    SalaireController::class,
                    'valider'
                ])->name('valider');

                /*
                 * Annulation.
                 */
                Route::put('/{salaire}/annuler', [
                    SalaireController::class,
                    'annuler'
                ])->name('annuler');

                /*
                 * Affichage.
                 */
                Route::get('/{salaire}', [
                    SalaireController::class,
                    'show'
                ])->name('show');

                /*
                 * Suppression d'un brouillon.
                 */
                Route::delete('/{salaire}', [
                    SalaireController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | PAIEMENTS DE SALAIRES
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('paiements-salaires')
            ->name('paiements-salaires.')
            ->group(function () {

                Route::get('/', [
                    PaiementSalaireController::class,
                    'index'
                ])->name('index');

                Route::get('/statistiques', [
                    PaiementSalaireController::class,
                    'statistiques'
                ])->name('statistiques');

                Route::get('/rapport', [
                    PaiementSalaireController::class,
                    'rapport'
                ])->name('rapport');

                Route::get('/{paiement}', [
                    PaiementSalaireController::class,
                    'show'
                ])->name('show');

                Route::get('/{paiement}/recu', [
                    PaiementSalaireController::class,
                    'recu'
                ])->name('recu');

                Route::get('/{paiement}/imprimer', [
                    PaiementSalaireController::class,
                    'imprimer'
                ])->name('imprimer');

                /*
                 * Modification refusée par la logique métier.
                 */
                Route::match(
                    ['put', 'patch'],
                    '/{paiement}',
                    [PaiementSalaireController::class, 'update']
                )->name('update');

                /*
                 * Suppression refusée par la logique métier.
                 */
                Route::delete('/{paiement}', [
                    PaiementSalaireController::class,
                    'destroy'
                ])->name('destroy');
            });


        /*
        |----------------------------------------------------------------------
        | COMMISSION DE CONTRÔLE
        |----------------------------------------------------------------------
        */

        Route::middleware('check.role:admin,super_admin')
            ->prefix('controles')
            ->name('controles.')
            ->group(function () {

                Route::get('/', [
                    ControleController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    ControleController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    ControleController::class,
                    'store'
                ])->name('store');

                Route::get('/{controle}', [
                    ControleController::class,
                    'show'
                ])->name('show');

                Route::get('/{controle}/edit', [
                    ControleController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{controle}', [
                    ControleController::class,
                    'update'
                ])->name('update');

                Route::put('/{controle}/valider', [
                    ControleController::class,
                    'valider'
                ])->name('valider');

                Route::put('/{controle}/rejeter', [
                    ControleController::class,
                    'rejeter'
                ])->name('rejeter');

                Route::delete('/{controle}', [
                    ControleController::class,
                    'destroy'
                ])->name('destroy');
            });
    });


/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES DYNAMIQUES
|--------------------------------------------------------------------------
*/


// Projets
Route::get('/projets', [
    PublicProjetController::class,
    'index'
])->name('projets.index');

Route::get('/projets/{slug}', [
    PublicProjetController::class,
    'show'
])->name('projets.show');


// Actualités
Route::get('/actualites', [
    ActualiteController::class,
    'index'
])->name('actualites.index');

Route::get('/actualites/{slug}', [
    ActualiteController::class,
    'show'
])->name('actualites.show');


// Témoignages
Route::get('/temoignages', [
    ActualiteController::class,
    'temoignages'
])->name('temoignages');


// Transparence
Route::get('/transparence', [
    ActualiteController::class,
    'transparence'
])->name('transparence');

Route::get('/transparence/financiers', function () {
    return redirect()->route('transparence', [
        'tab' => 'financiers'
    ]);
})->name('transparence.financiers');

Route::get('/transparence/annuels', function () {
    return redirect()->route('transparence', [
        'tab' => 'annuels'
    ]);
})->name('transparence.annuels');

Route::get('/transparence/documents', function () {
    return redirect()->route('transparence', [
        'tab' => 'documents'
    ]);
})->name('transparence.documents');


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


// Contact public
Route::get('/contactez-nous', [
    PublicContactController::class,
    'index'
])->name('contact');

Route::post('/contactez-nous', [
    PublicContactController::class,
    'store'
])->name('contact.store');


// Recherche
Route::get('/recherche', [
    RechercheController::class,
    'index'
])->name('recherche');


// Faire un don
Route::get('/faire-un-don', [
    DonController::class,
    'index'
])->name('don');

Route::post('/faire-un-don', [
    DonController::class,
    'store'
])->name('don.store');

Route::get('/don/merci', [
    DonController::class,
    'merci'
])->name('don.merci');


// Devenir membre
Route::get('/devenir-membre', [
    PublicMembreController::class,
    'index'
])->name('devenir-membre');

Route::post('/devenir-membre', [
    PublicMembreController::class,
    'store'
])->name('devenir-membre.store');

Route::get('/devenir-membre/confirmation', [
    PublicMembreController::class,
    'confirmation'
])->name('devenir-membre.confirmation');


// Newsletter
Route::post('/newsletter/subscribe', function () {
    return 'Abonnement newsletter';
})->name('newsletter.subscribe');