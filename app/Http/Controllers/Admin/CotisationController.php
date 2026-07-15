<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotisation;
use App\Models\Membre;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CotisationController extends Controller
{
    /**
     * Nombre d'éléments par page.
     */
    protected int $perPage = 15;

    /**
     * Affiche le tableau de bord et la liste globale des cotisations.
     */
    public function index(Request $request)
    {
        $query = Cotisation::with('membre');

        // Filtre par mois
        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        // Filtre par membre
        if ($request->filled('membre_id')) {
            $query->where('membre_id', $request->membre_id);
        }

        // Filtre par mode de paiement
        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        // Recherche par nom de membre
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->whereHas('membre', function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('post_nom', 'like', '%' . $recherche . '%')
                  ->orWhere('prenom', 'like', '%' . $recherche . '%');
            });
        }

        $cotisations = $query->orderBy('mois', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalCollecte = Cotisation::sum('montant');
        $moisEnCours = now()->format('Y-m');
        $collecteMois = Cotisation::where('mois', $moisEnCours)->sum('montant');
        $totalMembresActifs = Membre::where('statut', 'actif')->count();
        $membresAyantPaye = Cotisation::where('mois', $moisEnCours)->distinct('membre_id')->count();
        $tauxRecouvrement = $totalMembresActifs > 0 ? round(($membresAyantPaye / $totalMembresActifs) * 100) : 0;

        // Membres en retard (actifs n'ayant pas payé le mois en cours)
        $membresEnRetard = Membre::where('statut', 'actif')
            ->whereDoesntHave('cotisations', function ($q) use ($moisEnCours) {
                $q->where('mois', $moisEnCours);
            })->count();

        // ============================================
        // DONNÉES GRAPHIQUE BARRES (12 derniers mois)
        // ============================================
        $moisLabels = [];
        $moisValues = [];
        $moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisKey = $date->format('Y-m');
            $moisLabels[] = $moisNoms[$date->month - 1] . ' ' . $date->format('Y');
            $moisValues[] = Cotisation::where('mois', $moisKey)->sum('montant');
        }

        // ============================================
        // DONNÉES GRAPHIQUE ANNEAU (par mode de paiement)
        // ============================================
        $modesStats = Cotisation::selectRaw('mode_paiement, SUM(montant) as total')
            ->groupBy('mode_paiement')
            ->get();

        $modesLabels = [];
        $modesValues = [];
        $modesColors = [
            'especes' => '#28a745',
            'virement_bancaire' => '#16558F',
            'mobile_money' => '#e67e22',
            'autre' => '#8e44ad',
        ];

        foreach ($modesStats as $stat) {
            $modesLabels[] = match($stat->mode_paiement) {
                'especes' => 'Espèces',
                'virement_bancaire' => 'Virement bancaire',
                'mobile_money' => 'Mobile Money',
                'autre' => 'Autre',
                default => $stat->mode_paiement,
            };
            $modesValues[] = (float) $stat->total;
        }

        // Membres pour le filtre
        $membres = Membre::orderBy('nom')->get();
        $moisDisponibles = Cotisation::select('mois')->distinct()->orderBy('mois', 'desc')->pluck('mois');

        return view('admin.cotisations.index', compact(
            'cotisations',
            'totalCollecte',
            'collecteMois',
            'totalMembresActifs',
            'membresAyantPaye',
            'tauxRecouvrement',
            'membresEnRetard',
            'moisLabels',
            'moisValues',
            'modesLabels',
            'modesValues',
            'modesColors',
            'membres',
            'moisDisponibles',
            'moisEnCours'
        ));
    }

    /**
     * Affiche le formulaire d'enregistrement d'un paiement.
     */
    public function create()
    {
        $membres = Membre::where('statut', 'actif')->orderBy('nom')->get();
        $moisEnCours = now()->format('Y-m');
        $moisNoms = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $moisNoms[$date->format('Y-m')] = Carbon::createFromFormat('Y-m', $date->format('Y-m'))->translatedFormat('F Y');
        }

        return view('admin.cotisations.create', compact('membres', 'moisEnCours', 'moisNoms'));
    }

    /**
     * Enregistre un nouveau paiement de cotisation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'membre_id' => ['required', 'exists:membres,id'],
            'mois' => ['required', 'string', 'size:7'],
            'montant' => ['required', 'numeric', 'min:1', 'max:10000'],
            'date_paiement' => ['required', 'date'],
            'mode_paiement' => ['required', 'in:especes,virement_bancaire,mobile_money,autre'],
            'reference' => ['nullable', 'string', 'max:255'],
            'observation' => ['nullable', 'string', 'max:1000'],
        ], [
            'membre_id.required' => 'Veuillez sélectionner un membre.',
            'membre_id.exists' => 'Le membre sélectionné n\'existe pas.',
            'mois.required' => 'Veuillez sélectionner le mois.',
            'montant.required' => 'Veuillez indiquer le montant.',
            'date_paiement.required' => 'Veuillez indiquer la date de paiement.',
            'mode_paiement.required' => 'Veuillez sélectionner le mode de paiement.',
        ]);

        // Vérifier si le membre a déjà payé pour ce mois
        $existeDeja = Cotisation::where('membre_id', $validated['membre_id'])
            ->where('mois', $validated['mois'])
            ->exists();

        if ($existeDeja) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ce membre a déjà payé sa cotisation pour le mois sélectionné.');
        }

        $validated['enregistre_par'] = auth()->id();

        Cotisation::create($validated);

        $membre = Membre::find($validated['membre_id']);

        return redirect()->route('admin.cotisations.index')
            ->with('success', 'Cotisation de ' . number_format($validated['montant'], 2) . ' $ enregistrée pour ' . $membre->nomComplet() . '.');
    }

    /**
 * Affiche la liste des membres en retard de cotisation.
 */
public function retard(Request $request)
{
    $moisEnCours = $request->input('mois', now()->format('Y-m'));
    
    // Membres actifs n'ayant pas payé pour le mois sélectionné
    $membresEnRetard = Membre::where('statut', 'actif')
        ->whereDoesntHave('cotisations', function ($q) use ($moisEnCours) {
            $q->where('mois', $moisEnCours);
        })
        ->with(['cotisations' => function ($q) {
            $q->orderBy('mois', 'desc');
        }])
        ->orderBy('nom')
        ->paginate(15)
        ->withQueryString();

    // Statistiques
    $totalActifs = Membre::where('statut', 'actif')->count();
    $totalEnRetard = $membresEnRetard->total();
    $totalEnRegle = $totalActifs - $totalEnRetard;
    $tauxPaiement = $totalActifs > 0 ? round(($totalEnRegle / $totalActifs) * 100) : 0;

    // Mois disponibles pour le filtre
    $moisDisponibles = Cotisation::select('mois')->distinct()->orderBy('mois', 'desc')->pluck('mois');

    return view('admin.cotisations.retard', compact(
        'membresEnRetard',
        'totalActifs',
        'totalEnRetard',
        'totalEnRegle',
        'tauxPaiement',
        'moisEnCours',
        'moisDisponibles'
    ));
}


/**
 * Affiche la liste des membres en règle (ayant payé pour un mois donné).
 */
public function regle(Request $request)
{
    $moisEnCours = $request->input('mois', now()->format('Y-m'));
    
    // Membres actifs AYANT payé pour le mois sélectionné
    $membresEnRegle = Membre::where('statut', 'actif')
        ->whereHas('cotisations', function ($q) use ($moisEnCours) {
            $q->where('mois', $moisEnCours);
        })
        ->with(['cotisations' => function ($q) use ($moisEnCours) {
            $q->where('mois', $moisEnCours);
        }])
        ->orderBy('nom')
        ->paginate(15)
        ->withQueryString();

    // Statistiques
    $totalActifs = Membre::where('statut', 'actif')->count();
    $totalEnRegle = $membresEnRegle->total();
    $totalEnRetard = $totalActifs - $totalEnRegle;
    $tauxPaiement = $totalActifs > 0 ? round(($totalEnRegle / $totalActifs) * 100) : 0;

    $moisDisponibles = Cotisation::select('mois')->distinct()->orderBy('mois', 'desc')->pluck('mois');

    return view('admin.cotisations.regle', compact(
        'membresEnRegle',
        'totalActifs',
        'totalEnRegle',
        'totalEnRetard',
        'tauxPaiement',
        'moisEnCours',
        'moisDisponibles'
    ));
}
}