<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membre;
use App\Models\Organe;
use App\Models\Salaire;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaireController extends Controller
{
    /**
     * Nombre de salaires par page.
     *
     * @var int
     */
    protected int $perPage = 15;

    /**
     * Affiche la liste paginée et filtrée des salaires.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Salaire::with(['membre', 'organe']);

        // Filtre par mois
        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par membre
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('reference', 'like', '%' . $recherche . '%')
                  ->orWhereHas('membre', function ($mq) use ($recherche) {
                      $mq->where('nom', 'like', '%' . $recherche . '%')
                         ->orWhere('post_nom', 'like', '%' . $recherche . '%')
                         ->orWhere('prenom', 'like', '%' . $recherche . '%');
                  });
            });
        }

        $salaires = $query->orderBy('mois', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $moisCourant = now()->format('Y-m');
        $totalSalairesMois = Salaire::where('mois', $moisCourant)->count();
        $totalPayesMois = Salaire::where('mois', $moisCourant)->where('statut', 'paye')->count();
        $totalValidesMois = Salaire::where('mois', $moisCourant)->where('statut', 'valide')->count();
        $totalNetMois = Salaire::where('mois', $moisCourant)
            ->whereIn('statut', ['valide', 'paye'])
            ->sum('salaire_net');

        // Mois disponibles pour le filtre
        $moisDisponibles = Salaire::select('mois')
            ->distinct()
            ->orderBy('mois', 'desc')
            ->pluck('mois');

        return view('admin.salaires.index', compact(
            'salaires',
            'totalSalairesMois',
            'totalPayesMois',
            'totalValidesMois',
            'totalNetMois',
            'moisDisponibles',
            'moisCourant'
        ));
    }

    /**
     * Affiche le formulaire de création d'un salaire.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $membres = Membre::where('statut', 'actif')->orderBy('nom')->orderBy('post_nom')->get();
        $organes = Organe::where('actif', true)->with('membre')->orderBy('ordre')->get();
        $moisCourant = now()->format('Y-m');
        $referenceAuto = Salaire::genererReference($moisCourant);

        return view('admin.salaires.create', compact(
            'membres',
            'organes',
            'moisCourant',
            'referenceAuto'
        ));
    }

    /**
     * Enregistre un nouveau salaire.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'membre_id' => ['required', 'exists:membres,id'],
            'organe_id' => ['nullable', 'exists:organes,id'],
            'mois' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'salaire_base' => ['required', 'numeric', 'min:0'],
            'primes' => ['nullable', 'numeric', 'min:0'],
            'retenues' => ['nullable', 'numeric', 'min:0'],
            'mode_paiement' => ['required', 'in:especes,virement,mobile_money,cheque'],
            'statut' => ['required', 'in:brouillon,valide,paye'],
            'date_paiement' => ['nullable', 'date', 'required_if:statut,paye'],
            'reference' => ['required', 'string', 'max:50', 'unique:salaires,reference'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'membre_id.required' => 'Veuillez sélectionner un membre.',
            'mois.required' => 'Le mois est obligatoire.',
            'mois.regex' => 'Le mois doit être au format YYYY-MM.',
            'salaire_base.required' => 'Le salaire de base est obligatoire.',
            'salaire_base.min' => 'Le salaire de base ne peut pas être négatif.',
            'reference.unique' => 'Cette référence est déjà utilisée.',
            'date_paiement.required_if' => 'La date de paiement est obligatoire si le statut est "Payé".',
        ]);

        // Vérifier l'unicité membre + mois
        $existe = Salaire::where('membre_id', $validated['membre_id'])
            ->where('mois', $validated['mois'])
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Un salaire existe déjà pour ce membre pour le mois ' . $validated['mois'] . '.')
                ->withInput();
        }

        // Calculer le salaire net
        $validated['salaire_net'] = $validated['salaire_base']
            + ($validated['primes'] ?? 0)
            - ($validated['retenues'] ?? 0);

        // Si payé, définir qui a payé
        if ($validated['statut'] === 'paye') {
            $validated['paye_par'] = auth()->id();
        }

        // Si validé ou payé, définir qui a validé
        if (in_array($validated['statut'], ['valide', 'paye'])) {
            $validated['valide_par'] = auth()->id();
        }

        $salaire = Salaire::create($validated);

        return redirect()->route('admin.salaires.show', $salaire)
            ->with('success', 'Le salaire de ' . $salaire->nomMembre() . ' pour ' . $salaire->mois . ' a été créé.');
    }

    /**
     * Affiche la fiche détaillée d'un salaire.
     *
     * @param Salaire $salaire
     * @return \Illuminate\View\View
     */
    public function show(Salaire $salaire)
    {
        $salaire->load(['membre', 'organe', 'validePar', 'payePar']);

        return view('admin.salaires.show', compact('salaire'));
    }

    /**
     * Affiche le formulaire de modification d'un salaire.
     *
     * @param Salaire $salaire
     * @return \Illuminate\View\View
     */
    public function edit(Salaire $salaire)
    {
        $membres = Membre::where('statut', 'actif')->orderBy('nom')->orderBy('post_nom')->get();
        $organes = Organe::where('actif', true)->with('membre')->orderBy('ordre')->get();

        return view('admin.salaires.edit', compact('salaire', 'membres', 'organes'));
    }

    /**
     * Met à jour un salaire.
     *
     * @param Request $request
     * @param Salaire $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Salaire $salaire)
    {
        // Si le salaire est déjà payé, ne pas permettre la modification
        if ($salaire->estPaye()) {
            return redirect()->back()
                ->with('error', 'Impossible de modifier un salaire déjà payé.');
        }

        $validated = $request->validate([
            'organe_id' => ['nullable', 'exists:organes,id'],
            'salaire_base' => ['required', 'numeric', 'min:0'],
            'primes' => ['nullable', 'numeric', 'min:0'],
            'retenues' => ['nullable', 'numeric', 'min:0'],
            'mode_paiement' => ['required', 'in:especes,virement,mobile_money,cheque'],
            'statut' => ['required', 'in:brouillon,valide,paye'],
            'date_paiement' => ['nullable', 'date', 'required_if:statut,paye'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'salaire_base.required' => 'Le salaire de base est obligatoire.',
            'date_paiement.required_if' => 'La date de paiement est obligatoire si le statut est "Payé".',
        ]);

        // Calculer le salaire net
        $validated['salaire_net'] = $validated['salaire_base']
            + ($validated['primes'] ?? 0)
            - ($validated['retenues'] ?? 0);

        // Si payé, définir qui a payé
        if ($validated['statut'] === 'paye' && !$salaire->paye_par) {
            $validated['paye_par'] = auth()->id();
        }

        // Si validé ou payé, définir qui a validé
        if (in_array($validated['statut'], ['valide', 'paye']) && !$salaire->valide_par) {
            $validated['valide_par'] = auth()->id();
        }

        $salaire->update($validated);

        return redirect()->route('admin.salaires.show', $salaire)
            ->with('success', 'Le salaire a été mis à jour avec succès.');
    }

    /**
     * Valide un salaire en brouillon.
     *
     * @param Salaire $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function valider(Salaire $salaire)
    {
        if (!$salaire->estBrouillon()) {
            return redirect()->back()
                ->with('error', 'Ce salaire ne peut pas être validé.');
        }

        $salaire->valider(auth()->id());

        return redirect()->back()
            ->with('success', 'Le salaire a été validé avec succès.');
    }

    /**
     * Marque un salaire comme payé.
     *
     * @param Request $request
     * @param Salaire $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marquerPaye(Request $request, Salaire $salaire)
    {
        if ($salaire->estPaye()) {
            return redirect()->back()
                ->with('error', 'Ce salaire est déjà payé.');
        }

        $validated = $request->validate([
            'date_paiement' => ['required', 'date'],
        ]);

        $salaire->marquerPaye(auth()->id(), $validated['date_paiement']);

        return redirect()->back()
            ->with('success', 'Le salaire a été marqué comme payé.');
    }

    /**
     * Annule un salaire.
     *
     * @param Salaire $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function annuler(Salaire $salaire)
    {
        if ($salaire->estPaye()) {
            return redirect()->back()
                ->with('error', 'Impossible d\'annuler un salaire déjà payé.');
        }

        $salaire->annuler();

        return redirect()->back()
            ->with('success', 'Le salaire a été annulé.');
    }

    /**
     * Supprime un salaire.
     *
     * @param Salaire $salaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Salaire $salaire)
    {
        if ($salaire->estPaye()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer un salaire déjà payé.');
        }

        $salaire->delete();

        return redirect()->route('admin.salaires.index')
            ->with('success', 'Le salaire a été supprimé définitivement.');
    }

    /**
     * Affiche la grille salariale par poste.
     *
     * @return \Illuminate\View\View
     */
    public function grille()
    {
        // Organes actifs avec leurs salaires
        $organes = Organe::where('actif', true)
            ->with(['membre', 'salaires' => function ($query) {
                $query->orderBy('mois', 'desc')->take(3);
            }])
            ->get()
            ->groupBy('organe');

        return view('admin.salaires.grille', compact('organes'));
    }
}