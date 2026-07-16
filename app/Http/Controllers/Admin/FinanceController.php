<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotisation;
use App\Models\Don;
use App\Models\RapportFinancier;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    /**
     * Affiche le tableau de bord financier complet.
     */
    public function index(Request $request)
    {
        // ============================================
        // KPI FINANCIERS
        // ============================================
        $totalDons = Don::where('statut', 'confirme')->sum('montant');
        $totalCotisations = Cotisation::sum('montant');
        $totalEntrees = $totalDons + $totalCotisations;

        $totalDonsMois = Don::where('statut', 'confirme')
            ->whereMonth('date_don', now()->month)
            ->whereYear('date_don', now()->year)
            ->sum('montant');
        $totalCotisationsMois = Cotisation::where('mois', now()->format('Y-m'))->sum('montant');
        $totalEntreesMois = $totalDonsMois + $totalCotisationsMois;

        // Sorties (dépenses projets = budget utilisé)
        $totalSorties = \App\Models\Projet::sum('budget_utilise');
        $totalSortiesMois = $totalSorties; // Simplifié, on pourrait affiner

        $solde = $totalEntrees - $totalSorties;
        $soldeMois = $totalEntreesMois;

        $nombreDonateurs = Don::where('statut', 'confirme')->distinct('donateur_email')->count('donateur_email');
        $nombreMembresCotisations = Cotisation::where('mois', now()->format('Y-m'))->distinct('membre_id')->count();

        // ============================================
        // DONNÉES GRAPHIQUE BARRES EMPILÉES (12 mois)
        // ============================================
        $moisLabels = [];
        $donsMensuels = [];
        $cotisationsMensuelles = [];
        $moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisLabels[] = $moisNoms[$date->month - 1] . ' ' . $date->format('Y');
            $donsMensuels[] = Don::where('statut', 'confirme')
                ->whereYear('date_don', $date->year)
                ->whereMonth('date_don', $date->month)
                ->sum('montant');
            $cotisationsMensuelles[] = Cotisation::where('mois', $date->format('Y-m'))->sum('montant');
        }

        // ============================================
        // DONNÉES GRAPHIQUE ANNEAU ENTRÉES
        // ============================================
        $entreesLabels = ['Dons', 'Cotisations'];
        $entreesValues = [$totalDons, $totalCotisations];
        $entreesColors = ['#28a745', '#16558F'];

        // ============================================
        // LISTE DES RAPPORTS GÉNÉRÉS
        // ============================================
        $rapports = RapportFinancier::orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.finances.index', compact(
            'totalDons',
            'totalCotisations',
            'totalEntrees',
            'totalSorties',
            'solde',
            'totalEntreesMois',
            'totalDonsMois',
            'totalCotisationsMois',
            'nombreDonateurs',
            'nombreMembresCotisations',
            'moisLabels',
            'donsMensuels',
            'cotisationsMensuelles',
            'entreesLabels',
            'entreesValues',
            'entreesColors',
            'rapports'
        ));
    }

    /**
     * Affiche le formulaire de création d'un rapport financier.
     */
    public function create()
    {
        return view('admin.finances.create');
    }

    /**
     * Enregistre un nouveau rapport financier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:mensuel,trimestriel,annuel,projet,audit'],
            'periode' => ['required', 'string', 'max:7'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'total_entrees' => ['nullable', 'numeric', 'min:0'],
            'total_sorties' => ['nullable', 'numeric', 'min:0'],
            'dons_recus' => ['nullable', 'numeric', 'min:0'],
            'cotisations_recues' => ['nullable', 'numeric', 'min:0'],
            'subventions_recues' => ['nullable', 'numeric', 'min:0'],
            'autres_entrees' => ['nullable', 'numeric', 'min:0'],
            'depenses_projets' => ['nullable', 'numeric', 'min:0'],
            'salaires' => ['nullable', 'numeric', 'min:0'],
            'logistique' => ['nullable', 'numeric', 'min:0'],
            'administratif' => ['nullable', 'numeric', 'min:0'],
            'autres_sorties' => ['nullable', 'numeric', 'min:0'],
            'commentaire' => ['nullable', 'string', 'max:5000'],
            'public' => ['nullable', 'boolean'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'type.required' => 'Le type de rapport est obligatoire.',
            'periode.required' => 'La période est obligatoire.',
        ]);

        // Calculer le solde
        $validated['total_entrees'] = ($validated['dons_recus'] ?? 0) + ($validated['cotisations_recues'] ?? 0) + ($validated['subventions_recues'] ?? 0) + ($validated['autres_entrees'] ?? 0);
        $validated['total_sorties'] = ($validated['depenses_projets'] ?? 0) + ($validated['salaires'] ?? 0) + ($validated['logistique'] ?? 0) + ($validated['administratif'] ?? 0) + ($validated['autres_sorties'] ?? 0);
        $validated['solde'] = $validated['total_entrees'] - $validated['total_sorties'];
        $validated['public'] = $request->boolean('public');
        $validated['prepare_par'] = auth()->id();
        $validated['statut'] = 'brouillon';

        RapportFinancier::create($validated);

        return redirect()->route('admin.finances.index')
            ->with('success', 'Le rapport financier a été créé avec succès.');
    }

    /**
     * Affiche le détail d'un rapport financier.
     */
    public function show(RapportFinancier $rapport)
    {
        return view('admin.finances.show', compact('rapport'));
    }

    /**
     * Publie un rapport financier (le rend visible publiquement).
     */
    public function publier(RapportFinancier $rapport)
    {
        $rapport->update(['public' => true, 'statut' => 'publie', 'date_validation' => now()]);

        return redirect()->back()
            ->with('success', 'Le rapport a été publié. Il est maintenant visible sur la page Transparence.');
    }

    /**
     * Télécharge le rapport financier au format PDF.
     */
    public function telechargerPdf(RapportFinancier $rapport)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $html = view('admin.finances.rapport-pdf', compact('rapport'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Rapport-financier-' . \Str::slug($rapport->titre) . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    /**
     * Supprime un rapport financier.
     */
    public function destroy(RapportFinancier $rapport)
    {
        $rapport->delete();

        return redirect()->route('admin.finances.index')
            ->with('success', 'Le rapport financier a été supprimé.');
    }
}