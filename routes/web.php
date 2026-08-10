<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Public\ProjetController as PublicProjetController;
use App\Http\Controllers\Public\ActualiteController;
use App\Http\Controllers\Public\DonController;
use App\Http\Controllers\Public\MembreController as PublicMembreController;
use App\Http\Controllers\Public\ContactController as PublicContactController;
use App\Http\Controllers\Public\RechercheController;
use App\Http\Controllers\Admin\MembreController as AdminMembreController;
use App\Http\Controllers\Admin\CotisationController;
use App\Http\Controllers\Admin\GouvernanceController;
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

    Route::middleware('check.role:super_admin')->prefix('utilisateurs')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::put('/{user}/activer', [UserController::class, 'activer'])->name('activer');
        Route::put('/{user}/desactiver', [UserController::class, 'desactiver'])->name('desactiver');
        Route::put('/{user}/suspendre', [UserController::class, 'suspendre'])->name('suspendre');
        Route::get('/{user}/password', [UserController::class, 'editPassword'])->name('password');
        Route::put('/{user}/password', [UserController::class, 'updatePassword'])->name('password.update');
    });

    

    // Gestion des projets (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('projets')->name('projets.')->group(function () {
        Route::get('/', [AdminProjetController::class, 'index'])->name('index');
        Route::get('/create', [AdminProjetController::class, 'create'])->name('create');
        Route::get('/budgets', [AdminProjetController::class, 'budgets'])->name('budgets'); // ← ICI, AVANT {projet}
        Route::post('/', [AdminProjetController::class, 'store'])->name('store');
        
        Route::get('/{projet}', [AdminProjetController::class, 'show'])->name('show');
        Route::get('/{projet}/edit', [AdminProjetController::class, 'edit'])->name('edit');
        Route::put('/{projet}', [AdminProjetController::class, 'update'])->name('update');
        Route::delete('/{projet}', [AdminProjetController::class, 'destroy'])->name('destroy');
        Route::put('/{projet}/change-status', [AdminProjetController::class, 'changeStatus'])->name('changeStatus');
        Route::put('/{projet}/toggle-publier', [AdminProjetController::class, 'togglePublier'])->name('togglePublier');
        Route::put('/{projet}/budget', [AdminProjetController::class, 'updateBudget'])->name('updateBudget');
        
        // Indicateurs de projet
        Route::get('/{projet}/indicateurs', [IndicateurController::class, 'index'])->name('indicateurs.index');
        Route::post('/{projet}/indicateurs', [IndicateurController::class, 'store'])->name('indicateurs.store');
        Route::put('/{projet}/indicateurs/{indicateur}', [IndicateurController::class, 'update'])->name('indicateurs.update');
        Route::put('/{projet}/indicateurs/{indicateur}/value', [IndicateurController::class, 'updateValue'])->name('indicateurs.updateValue');
        Route::delete('/{projet}/indicateurs/{indicateur}', [IndicateurController::class, 'destroy'])->name('indicateurs.destroy');
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
        Route::get('/sanctions', [AdminMembreController::class, 'sanctions'])->name('sanctions');
        Route::put('/{membre}/reintegrer', [AdminMembreController::class, 'reintegrer'])->name('reintegrer');
        Route::put('/{membre}/suspendre', [AdminMembreController::class, 'suspendre'])->name('suspendre');
        Route::put('/{membre}/exclure', [AdminMembreController::class, 'exclure'])->name('exclure');
    });

    // Gestion des articles (tous les rôles)
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::put('/{article}/toggle-publier', [ArticleController::class, 'togglePublier'])->name('togglePublier');
        Route::put('/{article}/toggle-epingler', [ArticleController::class, 'toggleEpingler'])->name('toggleEpingler');
    });


    // Gestion des dons (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('dons')->name('dons.')->group(function () {
        Route::get('/', [AdminDonController::class, 'index'])->name('index');
        Route::get('/create', [AdminDonController::class, 'create'])->name('create');
        Route::post('/', [AdminDonController::class, 'store'])->name('store');
        Route::get('/{don}/telecharger-recus', [AdminDonController::class, 'telechargerRecu'])->name('telechargerRecu');
        Route::get('/{don}/recu', [AdminDonController::class, 'voirRecu'])->name('recu');
        Route::get('/{don}', [AdminDonController::class, 'show'])->name('show');
        Route::get('/{don}/edit', [AdminDonController::class, 'edit'])->name('edit');
        Route::put('/{don}', [AdminDonController::class, 'update'])->name('update');
        Route::delete('/{don}', [AdminDonController::class, 'destroy'])->name('destroy');
        Route::put('/{don}/generer-recu', [AdminDonController::class, 'genererRecu'])->name('genererRecu');
        Route::put('/{don}/change-status', [AdminDonController::class, 'changeStatus'])->name('changeStatus');
    });

    

    // Gestion des partenaires (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('partenaires')->name('partenaires.')->group(function () {
        Route::get('/', [PartenaireController::class, 'index'])->name('index');
        Route::get('/create', [PartenaireController::class, 'create'])->name('create');
        Route::post('/', [PartenaireController::class, 'store'])->name('store');
        Route::get('/{partenaire}', [PartenaireController::class, 'show'])->name('show');
        Route::get('/{partenaire}/edit', [PartenaireController::class, 'edit'])->name('edit');
        Route::put('/{partenaire}', [PartenaireController::class, 'update'])->name('update');
        Route::delete('/{partenaire}', [PartenaireController::class, 'destroy'])->name('destroy');
    });

    // Gestion des témoignages (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('temoignages')->name('temoignages.')->group(function () {
        Route::get('/', [TemoignageController::class, 'index'])->name('index');
        Route::get('/create', [TemoignageController::class, 'create'])->name('create');
        Route::post('/', [TemoignageController::class, 'store'])->name('store');
        Route::get('/{temoignage}', [TemoignageController::class, 'show'])->name('show');
        Route::get('/{temoignage}/edit', [TemoignageController::class, 'edit'])->name('edit');
        Route::put('/{temoignage}', [TemoignageController::class, 'update'])->name('update');
        Route::delete('/{temoignage}', [TemoignageController::class, 'destroy'])->name('destroy');
        Route::put('/{temoignage}/approuver', [TemoignageController::class, 'approuver'])->name('approuver');
        Route::put('/{temoignage}/rejeter', [TemoignageController::class, 'rejeter'])->name('rejeter');
        Route::put('/{temoignage}/archiver', [TemoignageController::class, 'archiver'])->name('archiver');
        Route::put('/{temoignage}/desarchiver', [TemoignageController::class, 'desarchiver'])->name('desarchiver');
    });

    // Gestion des médias (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('medias')->name('medias.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::get('/create', [MediaController::class, 'create'])->name('create');
        Route::post('/', [MediaController::class, 'store'])->name('store');
        Route::get('/{media}', [MediaController::class, 'show'])->name('show');
        Route::get('/{media}/edit', [MediaController::class, 'edit'])->name('edit');
        Route::put('/{media}', [MediaController::class, 'update'])->name('update');
        Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
        Route::put('/{media}/toggle-publier', [MediaController::class, 'togglePublier'])->name('togglePublier');
        Route::put('/{media}/replace-file', [MediaController::class, 'replaceFile'])->name('replaceFile');
        Route::post('/{media}/incrementer-vues', [MediaController::class, 'incrementerVues'])->name('incrementerVues');
        
        // Actions batch
        Route::delete('/batch/delete', [MediaController::class, 'batchDelete'])->name('batchDelete');
        Route::put('/batch/change-album', [MediaController::class, 'batchChangeAlbum'])->name('batchChangeAlbum');
        Route::put('/batch/toggle-publier', [MediaController::class, 'batchTogglePublier'])->name('batchTogglePublier');
    });

    

    // Gestion financière (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('finances')->name('finances.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::get('/create', [FinanceController::class, 'create'])->name('create');
        Route::post('/', [FinanceController::class, 'store'])->name('store');
        Route::get('/{rapport}', [FinanceController::class, 'show'])->name('show');
        Route::put('/{rapport}/publier', [FinanceController::class, 'publier'])->name('publier');
        Route::get('/{rapport}/pdf', [FinanceController::class, 'telechargerPdf'])->name('telechargerPdf');
        Route::delete('/{rapport}', [FinanceController::class, 'destroy'])->name('destroy');
    });

    // Gouvernance (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('gouvernance')->name('gouvernance.')->group(function () {
        Route::get('/', function () { return 'Gouvernance'; })->name('index');
    });

    // Messages de contact (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [AdminContactController::class, 'index'])->name('index');
        Route::get('/{email}', [AdminContactController::class, 'show'])->name('show');
        Route::put('/{contact}/lu', [AdminContactController::class, 'marquerLu'])->name('marquerLu');
        Route::put('/{contact}/non-lu', [AdminContactController::class, 'marquerNonLu'])->name('marquerNonLu');
        Route::put('/{contact}/statut', [AdminContactController::class, 'changeStatut'])->name('changeStatut');
        Route::put('/{contact}/archiver', [AdminContactController::class, 'archiver'])->name('archiver');
        Route::put('/{contact}/desarchiver', [AdminContactController::class, 'desarchiver'])->name('desarchiver');
        Route::put('/{contact}/note', [AdminContactController::class, 'note'])->name('note');
        Route::post('/{contact}/repondre', [AdminContactController::class, 'repondre'])->name('repondre');
        Route::put('/{contact}/assigner', [AdminContactController::class, 'assigner'])->name('assigner');
        Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->name('destroy');
        Route::delete('/conversation/supprimer', [AdminContactController::class, 'destroyConversation'])->name('destroyConversation');
    });

    // Messagerie interne (tous les utilisateurs connectés)
    Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/envoyes', [MessageController::class, 'envoyes'])->name('envoyes');
        Route::get('/composer', [MessageController::class, 'composer'])->name('composer');
        Route::post('/envoyer', [MessageController::class, 'envoyer'])->name('envoyer');
        Route::get('/{user}', [MessageController::class, 'show'])->name('show');
        Route::post('/{user}/repondre', [MessageController::class, 'repondre'])->name('repondre');
        Route::put('/{message}/lu', [MessageController::class, 'marquerLu'])->name('marquerLu');
        Route::put('/{user}/archiver', [MessageController::class, 'archiverConversation'])->name('archiverConversation');
        Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('check.role:super_admin')->prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/update', [SettingController::class, 'update'])->name('update');
        Route::post('/remove-image', [SettingController::class, 'removeImage'])->name('removeImage');
    });

    // Gestion des cotisations (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('cotisations')->name('cotisations.')->group(function () {
        Route::get('/', [CotisationController::class, 'index'])->name('index');
        Route::get('/create', [CotisationController::class, 'create'])->name('create');
        Route::post('/', [CotisationController::class, 'store'])->name('store');
        Route::get('/retard', [CotisationController::class, 'retard'])->name('retard');
        Route::get('/regle', [CotisationController::class, 'regle'])->name('regle');
    });

    

    // Gouvernance
    Route::middleware('check.role:admin,super_admin')->prefix('gouvernance')->name('gouvernance.')->group(function () {
        Route::get('/', [GouvernanceController::class, 'index'])->name('index');
        Route::get('/organes/{organe}', [GouvernanceController::class, 'organe'])->name('organe');
        Route::post('/organes/store', [GouvernanceController::class, 'storeMembre'])->name('storeMembre');
        Route::put('/organes/{organe}/update', [GouvernanceController::class, 'updateMembre'])->name('updateMembre');
        Route::put('/organes/{organe}/retirer', [GouvernanceController::class, 'retirerMembre'])->name('retirerMembre');
        
        // Assemblées Générales
        Route::get('/assemblies', [GouvernanceController::class, 'assemblies'])->name('assemblies');
        Route::get('/assemblies/create', [GouvernanceController::class, 'createAssembly'])->name('assemblies.create');
        Route::post('/assemblies', [GouvernanceController::class, 'storeAssembly'])->name('assemblies.store');
        Route::get('/assemblies/{assembly}', [GouvernanceController::class, 'showAssembly'])->name('assemblies.show');
        Route::get('/assemblies/{assembly}/edit', [GouvernanceController::class, 'editAssembly'])->name('assemblies.edit');
        Route::put('/assemblies/{assembly}', [GouvernanceController::class, 'updateAssembly'])->name('assemblies.update');
        Route::delete('/assemblies/{assembly}', [GouvernanceController::class, 'destroyAssembly'])->name('assemblies.destroy');
    });

    // Gestion des documents (admin et super_admin)
    Route::middleware('check.role:admin,super_admin')->prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::put('/{document}/replace-file', [DocumentController::class, 'replaceFile'])->name('replaceFile');
        Route::put('/{document}/toggle-public', [DocumentController::class, 'togglePublic'])->name('togglePublic');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview');
    });
        

});

/*
|--------------------------------------------------------------------------
| ROUTES DYNAMIQUES (PLACEHOLDERS POUR LE FUTUR)
|--------------------------------------------------------------------------
*/

// Projets (liste et détail)

Route::get('/projets', [PublicProjetController::class, 'index'])->name('projets.index');
Route::get('/projets/{slug}', [PublicProjetController::class, 'show'])->name('projets.show');

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

// Contact public
Route::get('/contactez-nous', [PublicContactController::class, 'index'])->name('contact');
Route::post('/contactez-nous', [PublicContactController::class, 'store'])->name('contact.store');

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