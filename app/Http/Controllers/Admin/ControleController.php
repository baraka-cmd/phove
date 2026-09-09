<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ControleTrimestriel;
use App\Models\Don;
use App\Models\Cotisation;
use App\Models\RapportFinancier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ControleController extends Controller
{
    /**
     * Nombre de contrôles par page.
     *
     * @var int
     */
    protected int $perPage = 15;

    /**
     * Affiche la liste paginée et filtrée des contrôles trimestriels.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ControleTrimestriel::with(['controlePar', 'validePar']);

        // Filtre par année
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        // Filtre par trimestre
        if ($request->filled('trimestre')) {
            $query->where('trimestre', $request->trimestre);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type_controle', $request->type);
        }

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('reference', 'like', '%' . $recherche . '%')
                  ->orWhere('controleur', 'like', '%' . $recherche . '%')
                  ->orWhere('observations', 'like', '%' . $recherche . '%');
            });
        }

        $controles = $query->orderBy('annee', 'desc')
            ->orderBy('trimestre', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $stats = ControleTrimestriel::statsAnnuelle(now()->year);
        $totalControles = ControleTrimestriel::count();
        $controlesValides = ControleTrimestriel::where('statut', 'valide')->count();
        $controlesNonConformes = ControleTrimestriel::where('conformite', false)->count();

        // Années disponibles
        $annees = ControleTrimestriel::select('annee')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        return view('admin.controles.index', compact(
            'controles',
            'stats',
            'totalControles',
            'controlesValides',
            'controlesNonConformes',
            'annees'
        ));
    }

    /**
     * Affiche le formulaire de création d'un contrôle.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $trimestres = ControleTrimestriel::TRIMESTRES;
        $types = ControleTrimestriel::TYPES;
        $statuts = ControleTrimestriel::STATUTS;

        $anneeCourante = now()->year;
        $trimestreCourant = $this->getTrimestreCourant();
        $referenceAuto = ControleTrimestriel::genererReference($anneeCourante, $trimestreCourant);

        // Dates du trimestre courant
        $dates = $this->getDatesTrimestre($anneeCourante, $trimestreCourant);

        return view('admin.controles.create', compact(
            'trimestres',
            'types',
            'statuts',
            'anneeCourante',
            'trimestreCourant',
            'referenceAuto',
            'dates'
        ));
    }

    /**
     * Enregistre un nouveau contrôle.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:30', 'unique:controles_trimestriels,reference'],
            'trimestre' => ['required', 'in:T1,T2,T3,T4'],
            'annee' => ['required', 'integer', 'min:2000', 'max:2100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'date_controle' => ['required', 'date'],
            'type_controle' => ['required', 'in:financier,operationnel,global'],
            'statut' => ['required', 'in:planifie,en_cours,termine,valide,rejete'],
            'controleur' => ['nullable', 'string', 'max:255'],
            'total_entrees' => ['nullable', 'numeric', 'min:0'],
            'total_sorties' => ['nullable', 'numeric', 'min:0'],
            'conformite' => ['nullable', 'boolean'],
            'documents_verifies' => ['nullable', 'string', 'max:5000'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'recommandations' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'reference.required' => 'La référence est obligatoire.',
            'reference.unique' => 'Cette référence est déjà utilisée.',
            'trimestre.required' => 'Le trimestre est obligatoire.',
            'annee.required' => 'L\'année est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ]);

        // Vérifier l'unicité trimestre + année
        $existe = ControleTrimestriel::where('trimestre', $validated['trimestre'])
            ->where('annee', $validated['annee'])
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Un contrôle existe déjà pour le trimestre ' . $validated['trimestre'] . ' ' . $validated['annee'] . '.')
                ->withInput();
        }

        $validated['ecart'] = ($validated['total_entrees'] ?? 0) - ($validated['total_sorties'] ?? 0);
        $validated['conformite'] = $request->boolean('conformite');
        $validated['controle_par'] = auth()->id();

        if ($validated['statut'] === 'valide') {
            $validated['valide_par'] = auth()->id();
            $validated['date_validation'] = now()->format('Y-m-d');
        }

        $controle = ControleTrimestriel::create($validated);

        return redirect()->route('admin.controles.show', $controle)
            ->with('success', 'Le contrôle ' . $controle->reference . ' a été créé avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un contrôle.
     *
     * @param ControleTrimestriel $controle
     * @return \Illuminate\View\View
     */
    public function show(ControleTrimestriel $controle)
    {
        $controle->load(['controlePar', 'validePar']);

        return view('admin.controles.show', compact('controle'));
    }

    /**
     * Affiche le formulaire de modification d'un contrôle.
     *
     * @param ControleTrimestriel $controle
     * @return \Illuminate\View\View
     */
    public function edit(ControleTrimestriel $controle)
    {
        $trimestres = ControleTrimestriel::TRIMESTRES;
        $types = ControleTrimestriel::TYPES;
        $statuts = ControleTrimestriel::STATUTS;

        return view('admin.controles.edit', compact('controle', 'trimestres', 'types', 'statuts'));
    }

    /**
     * Met à jour un contrôle.
     *
     * @param Request $request
     * @param ControleTrimestriel $controle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ControleTrimestriel $controle)
    {
        // Si le contrôle est validé, ne pas permettre la modification
        if ($controle->estValide()) {
            return redirect()->back()
                ->with('error', 'Impossible de modifier un contrôle déjà validé.');
        }

        $validated = $request->validate([
            'trimestre' => ['required', 'in:T1,T2,T3,T4'],
            'annee' => ['required', 'integer', 'min:2000', 'max:2100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'date_controle' => ['required', 'date'],
            'type_controle' => ['required', 'in:financier,operationnel,global'],
            'statut' => ['required', 'in:planifie,en_cours,termine,valide,rejete'],
            'controleur' => ['nullable', 'string', 'max:255'],
            'total_entrees' => ['nullable', 'numeric', 'min:0'],
            'total_sorties' => ['nullable', 'numeric', 'min:0'],
            'conformite' => ['nullable', 'boolean'],
            'documents_verifies' => ['nullable', 'string', 'max:5000'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'recommandations' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $validated['ecart'] = ($validated['total_entrees'] ?? 0) - ($validated['total_sorties'] ?? 0);
        $validated['conformite'] = $request->boolean('conformite');

        if ($validated['statut'] === 'valide' && !$controle->valide_par) {
            $validated['valide_par'] = auth()->id();
            $validated['date_validation'] = now()->format('Y-m-d');
        }

        $controle->update($validated);

        return redirect()->route('admin.controles.show', $controle)
            ->with('success', 'Le contrôle a été mis à jour avec succès.');
    }

    /**
     * Valide un contrôle.
     *
     * @param ControleTrimestriel $controle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function valider(ControleTrimestriel $controle)
    {
        if ($controle->estValide()) {
            return redirect()->back()
                ->with('error', 'Ce contrôle est déjà validé.');
        }

        $controle->valider(auth()->id());

        return redirect()->back()
            ->with('success', 'Le contrôle a été validé avec succès.');
    }

    /**
     * Rejette un contrôle.
     *
     * @param Request $request
     * @param ControleTrimestriel $controle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejeter(Request $request, ControleTrimestriel $controle)
    {
        $validated = $request->validate([
            'motif' => ['required', 'string', 'max:500'],
        ]);

        $controle->rejeter(auth()->id(), $validated['motif']);

        return redirect()->back()
            ->with('success', 'Le contrôle a été rejeté.');
    }

    /**
     * Supprime un contrôle.
     *
     * @param ControleTrimestriel $controle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ControleTrimestriel $controle)
    {
        if ($controle->estValide()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer un contrôle validé.');
        }

        $controle->delete();

        return redirect()->route('admin.controles.index')
            ->with('success', 'Le contrôle a été supprimé définitivement.');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le trimestre courant (T1, T2, T3, T4).
     *
     * @return string
     */
    private function getTrimestreCourant(): string
    {
        $mois = (int) now()->format('n');
        return match(true) {
            $mois <= 3 => 'T1',
            $mois <= 6 => 'T2',
            $mois <= 9 => 'T3',
            default => 'T4',
        };
    }

    /**
     * Retourne les dates de début et fin d'un trimestre.
     *
     * @param int $annee
     * @param string $trimestre
     * @return array{debut: string, fin: string}
     */
    private function getDatesTrimestre(int $annee, string $trimestre): array
    {
        return match($trimestre) {
            'T1' => ['debut' => $annee . '-01-01', 'fin' => $annee . '-03-31'],
            'T2' => ['debut' => $annee . '-04-01', 'fin' => $annee . '-06-30'],
            'T3' => ['debut' => $annee . '-07-01', 'fin' => $annee . '-09-30'],
            'T4' => ['debut' => $annee . '-10-01', 'fin' => $annee . '-12-31'],
            default => ['debut' => $annee . '-01-01', 'fin' => $annee . '-12-31'],
        };
    }
}