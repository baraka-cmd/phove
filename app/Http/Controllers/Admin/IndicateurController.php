<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndicateurProjet;
use App\Models\Projet;
use Illuminate\Http\Request;

class IndicateurController extends Controller
{
    /**
     * Affiche la liste des indicateurs d'un projet avec statistiques.
     */
    public function index(Projet $projet)
    {
        $indicateurs = $projet->indicateurs()->orderBy('created_at', 'desc')->get();

        // Statistiques
        $totalIndicateurs = $indicateurs->count();
        $indicateursAtteints = $indicateurs->filter(fn($i) => $i->objectifAtteint())->count();
        $indicateursEnCours = $totalIndicateurs - $indicateursAtteints;

        // Taux global d'atteinte
        $tauxGlobal = $totalIndicateurs > 0
            ? round($indicateurs->avg(fn($i) => $i->pourcentage()))
            : 0;

        // Données pour le graphique en barres
        $graphLabels = $indicateurs->pluck('nom_indicateur')->map(fn($n) => \Str::limit($n, 30));
        $graphValues = $indicateurs->map(fn($i) => $i->pourcentage());
        $graphCibles = $indicateurs->map(fn($i) => 100);
        $graphColors = $indicateurs->map(fn($i) => $i->objectifAtteint() ? '#28a745' : '#16558F');

        // Données pour le graphique en anneau (atteints vs en cours)
        $donutLabels = ['Atteints', 'En cours'];
        $donutValues = [$indicateursAtteints, $indicateursEnCours];
        $donutColors = ['#28a745', '#16558F'];

        return view('admin.projets.indicateurs.index', compact(
            'projet',
            'indicateurs',
            'totalIndicateurs',
            'indicateursAtteints',
            'indicateursEnCours',
            'tauxGlobal',
            'graphLabels',
            'graphValues',
            'graphColors',
            'donutLabels',
            'donutValues',
            'donutColors'
        ));
    }

    /**
     * Enregistre un nouvel indicateur.
     */
    public function store(Request $request, Projet $projet)
    {
        $validated = $request->validate([
            'nom_indicateur' => ['required', 'string', 'max:255'],
            'valeur_cible' => ['required', 'integer', 'min:1'],
            'valeur_actuelle' => ['nullable', 'integer', 'min:0'],
            'unite' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'nom_indicateur.required' => 'Le nom de l\'indicateur est obligatoire.',
            'valeur_cible.required' => 'La valeur cible est obligatoire.',
            'valeur_cible.min' => 'La valeur cible doit être supérieure à 0.',
            'unite.required' => 'L\'unité est obligatoire.',
        ]);

        $validated['projet_id'] = $projet->id;
        $validated['valeur_actuelle'] = $validated['valeur_actuelle'] ?? 0;
        $validated['date_mise_a_jour'] = now();

        IndicateurProjet::create($validated);

        return redirect()->back()
            ->with('success', 'L\'indicateur a été ajouté avec succès.');
    }

    /**
     * Met à jour la valeur actuelle d'un indicateur.
     */
    public function update(Request $request, Projet $projet, IndicateurProjet $indicateur)
    {
        $validated = $request->validate([
            'nom_indicateur' => ['required', 'string', 'max:255'],
            'valeur_cible' => ['required', 'integer', 'min:1'],
            'valeur_actuelle' => ['required', 'integer', 'min:0'],
            'unite' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'nom_indicateur.required' => 'Le nom est obligatoire.',
            'valeur_cible.required' => 'La valeur cible est obligatoire.',
            'valeur_actuelle.required' => 'La valeur actuelle est obligatoire.',
            'unite.required' => 'L\'unité est obligatoire.',
        ]);

        $validated['date_mise_a_jour'] = now();
        $indicateur->update($validated);

        return redirect()->back()
            ->with('success', 'L\'indicateur a été mis à jour avec succès.');
    }

    /**
     * Met à jour rapidement la valeur actuelle (depuis la liste).
     */
    public function updateValue(Request $request, Projet $projet, IndicateurProjet $indicateur)
    {
        $validated = $request->validate([
            'valeur_actuelle' => ['required', 'integer', 'min:0'],
        ]);

        $indicateur->update([
            'valeur_actuelle' => $validated['valeur_actuelle'],
            'date_mise_a_jour' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'La valeur de l\'indicateur a été mise à jour.');
    }

    /**
     * Supprime un indicateur.
     */
    public function destroy(Projet $projet, IndicateurProjet $indicateur)
    {
        $indicateur->delete();

        return redirect()->back()
            ->with('success', 'L\'indicateur a été supprimé.');
    }
}