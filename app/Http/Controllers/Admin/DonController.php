<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Don;
use App\Models\Projet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

class DonController extends Controller
{
    /**
     * Nombre de dons par page.
     */
    protected int $perPage = 15;

    /**
     * Statuts des dons.
     */
    protected array $statuts = [
        'confirme' => 'Confirmé',
        'en_attente' => 'En attente',
        'annule' => 'Annulé',
    ];

    /**
     * Affiche la liste paginée et filtrée des dons avec statistiques.
     */
    public function index(Request $request)
    {
        $query = Don::with('projet');

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par projet
        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }

        // Filtre par mode de paiement
        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        // Filtre par type de don
        if ($request->filled('type_don')) {
            $query->where('type_don', $request->type_don);
        }

        // Filtre par date (mois)
        if ($request->filled('mois')) {
            $query->whereYear('date_don', substr($request->mois, 0, 4))
                  ->whereMonth('date_don', substr($request->mois, 5, 2));
        }

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('donateur_nom', 'like', '%' . $recherche . '%')
                  ->orWhere('donateur_email', 'like', '%' . $recherche . '%')
                  ->orWhere('reference_transaction', 'like', '%' . $recherche . '%')
                  ->orWhere('numero_recu', 'like', '%' . $recherche . '%');
            });
        }

        $dons = $query->orderBy('date_don', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalDons = Don::sum('montant');
        $totalDonsMois = Don::whereMonth('date_don', now()->month)
            ->whereYear('date_don', now()->year)
            ->sum('montant');
        $nombreDonateurs = Don::distinct('donateur_email')->count('donateur_email');
        $donsEnAttente = Don::where('statut', 'en_attente')->count();
        $totalDonsConfirmes = Don::where('statut', 'confirme')->sum('montant');
        $moyenneDon = Don::where('statut', 'confirme')->avg('montant') ?? 0;

        // ============================================
        // DONNÉES GRAPHIQUE BARRES (12 derniers mois)
        // ============================================
        $moisLabels = [];
        $moisValues = [];
        $moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisLabels[] = $moisNoms[$date->month - 1] . ' ' . $date->format('Y');
            $moisValues[] = Don::whereYear('date_don', $date->year)
                ->whereMonth('date_don', $date->month)
                ->sum('montant');
        }

        // ============================================
        // DONNÉES GRAPHIQUE ANNEAU (par mode de paiement)
        // ============================================
        $modesStats = Don::selectRaw('mode_paiement, SUM(montant) as total')
            ->groupBy('mode_paiement')
            ->get();

        $modesLabels = [];
        $modesValues = [];
        $modesColors = [
            'especes' => '#28a745',
            'virement_bancaire' => '#16558F',
            'mobile_money' => '#e67e22',
            'carte_bancaire' => '#17a2b8',
            'paypal' => '#8e44ad',
            'autre' => '#7f8c8d',
        ];

        foreach ($modesStats as $stat) {
            $modesLabels[] = match ($stat->mode_paiement) {
                'especes' => 'Espèces',
                'virement_bancaire' => 'Virement bancaire',
                'mobile_money' => 'Mobile Money',
                'carte_bancaire' => 'Carte bancaire',
                'paypal' => 'PayPal',
                'autre' => 'Autre',
                default => $stat->mode_paiement,
            };
            $modesValues[] = (float) $stat->total;
        }

        // Projets pour le filtre
        $projets = Projet::orderBy('titre')->get();

        // Mois disponibles pour le filtre
        $moisDisponibles = Don::selectRaw('DISTINCT DATE_FORMAT(date_don, "%Y-%m") as mois')
            ->orderBy('mois', 'desc')
            ->pluck('mois');

        return view('admin.dons.index', compact(
            'dons',
            'totalDons',
            'totalDonsMois',
            'nombreDonateurs',
            'donsEnAttente',
            'totalDonsConfirmes',
            'moyenneDon',
            'moisLabels',
            'moisValues',
            'modesLabels',
            'modesValues',
            'modesColors',
            'projets',
            'moisDisponibles'
        ));
    }

    /**
     * Affiche le formulaire d'enregistrement manuel d'un don.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.dons.create', compact('projets'));
    }

    /**
     * Enregistre un don manuellement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'donateur_nom' => ['required', 'string', 'max:255'],
            'donateur_email' => ['nullable', 'email', 'max:191'],
            'donateur_telephone' => ['nullable', 'string', 'max:20'],
            'donateur_adresse' => ['nullable', 'string', 'max:255'],
            'type_donateur' => ['required', 'in:particulier,organisation,etat,anonyme'],
            'montant' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'devise' => ['required', 'in:USD,CDF'],
            'type_don' => ['required', 'in:ponctuel,recurrent'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'mode_paiement' => ['required', 'in:especes,virement_bancaire,mobile_money,carte_bancaire,paypal,autre'],
            'reference_transaction' => ['nullable', 'string', 'max:255'],
            'date_don' => ['required', 'date'],
            'statut' => ['required', 'in:confirme,en_attente,annule'],
            'message_donateur' => ['nullable', 'string', 'max:1000'],
        ], [
            'donateur_nom.required' => 'Le nom du donateur est obligatoire.',
            'montant.required' => 'Le montant est obligatoire.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'date_don.required' => 'La date du don est obligatoire.',
        ]);

        $validated['enregistre_par'] = auth()->id();

        $don = Don::create($validated);

        // Générer un numéro de reçu
        if ($validated['statut'] === 'confirme') {
            $don->update([
                'numero_recu' => 'AHD-RDC-' . date('Y') . '-' . str_pad($don->id, 4, '0', STR_PAD_LEFT),
                'recu_genere' => true,
                'date_recu' => now(),
            ]);
        }

        return redirect()->route('admin.dons.index')
            ->with('success', 'Le don de ' . number_format($validated['montant'], 2) . ' $ a été enregistré avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un don.
     */
    public function show(Don $don)
    {
        $don->load('projet');
        return view('admin.dons.show', compact('don'));
    }

    /**
     * Affiche le formulaire de modification d'un don.
     */
    public function edit(Don $don)
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.dons.edit', compact('don', 'projets'));
    }

    /**
     * Met à jour un don.
     */
    public function update(Request $request, Don $don)
    {
        $validated = $request->validate([
            'donateur_nom' => ['required', 'string', 'max:255'],
            'donateur_email' => ['nullable', 'email', 'max:191'],
            'donateur_telephone' => ['nullable', 'string', 'max:20'],
            'donateur_adresse' => ['nullable', 'string', 'max:255'],
            'type_donateur' => ['required', 'in:particulier,organisation,etat,anonyme'],
            'montant' => ['required', 'numeric', 'min:1'],
            'devise' => ['required', 'in:USD,CDF'],
            'type_don' => ['required', 'in:ponctuel,recurrent'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'mode_paiement' => ['required', 'in:especes,virement_bancaire,mobile_money,carte_bancaire,paypal,autre'],
            'reference_transaction' => ['nullable', 'string', 'max:255'],
            'date_don' => ['required', 'date'],
            'statut' => ['required', 'in:confirme,en_attente,annule'],
            'message_donateur' => ['nullable', 'string', 'max:1000'],
        ]);

        // Si on confirme un don qui était en attente, générer le reçu
        if ($validated['statut'] === 'confirme' && !$don->numero_recu) {
            $validated['numero_recu'] = 'AHD-RDC-' . date('Y') . '-' . str_pad($don->id, 4, '0', STR_PAD_LEFT);
            $validated['recu_genere'] = true;
            $validated['date_recu'] = now();
        }

        $don->update($validated);

        return redirect()->route('admin.dons.index')
            ->with('success', 'Le don a été mis à jour avec succès.');
    }

    /**
     * Supprime un don.
     */
    public function destroy(Don $don)
    {
        $don->delete();

        return redirect()->route('admin.dons.index')
            ->with('success', 'Le don a été supprimé définitivement.');
    }

    /**
     * Génère un reçu fiscal pour un don.
     */
    public function genererRecu(Don $don)
    {
        if (!$don->numero_recu) {
            $don->update([
                'numero_recu' => 'AHD-RDC-' . date('Y') . '-' . str_pad($don->id, 4, '0', STR_PAD_LEFT),
                'recu_genere' => true,
                'date_recu' => now(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Le reçu fiscal n° ' . $don->numero_recu . ' a été généré avec succès.');
    }

    /**
     * Change le statut d'un don rapidement.
     */
    public function changeStatus(Request $request, Don $don)
    {
        $request->validate([
            'statut' => ['required', 'in:confirme,en_attente,annule'],
        ]);

        $don->update(['statut' => $request->statut]);

        // Générer le reçu si confirmé
        if ($request->statut === 'confirme' && !$don->numero_recu) {
            $don->update([
                'numero_recu' => 'AHD-RDC-' . date('Y') . '-' . str_pad($don->id, 4, '0', STR_PAD_LEFT),
                'recu_genere' => true,
                'date_recu' => now(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Le statut du don a été changé en "' . $this->statuts[$request->statut] . '".');
    }

    public function voirRecu(Don $don)
{
    if (!$don->numero_recu) {
        $don->update([
            'numero_recu' => 'AHD-RDC-' . date('Y') . '-' . str_pad($don->id, 4, '0', STR_PAD_LEFT),
            'recu_genere' => true,
            'date_recu' => now(),
        ]);
    }
    return view('admin.dons.recu', compact('don'));
}

/**
 * Télécharge le reçu fiscal au format PDF.
 */
/**
 * Affiche ou télécharge le reçu fiscal au format PDF.
 */
public function telechargerRecu(Don $don)
{
    // Générer le numéro de reçu s'il n'existe pas
    if (!$don->numero_recu) {
        $don->update([
            'numero_recu' => 'AHD-RDC-' . date('Y') . '-' . str_pad($don->id, 4, '0', STR_PAD_LEFT),
            'recu_genere' => true,
            'date_recu' => now(),
        ]);
        $don->refresh();
    }

    $don->load('projet');

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);
    $html = view('admin.dons.recu-pdf', compact('don'))->render();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = 'Recu-AHD-RDC-' . $don->numero_recu . '.pdf';

    // Si ?view=1 → afficher dans le navigateur, sinon → télécharger
    if (request()->has('view')) {
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    return response()->streamDownload(function () use ($dompdf) {
        echo $dompdf->output();
    }, $filename, ['Content-Type' => 'application/pdf']);
}
}