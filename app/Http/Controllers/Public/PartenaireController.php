<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    /**
     * Types de partenaires disponibles.
     *
     * @var array<string, string>
     */
    protected array $types = [
        'bailleur_fonds' => 'Bailleur de fonds',
        'technique' => 'Partenaire technique',
        'local' => 'Partenaire local',
        'international' => 'Partenaire international',
        'institutionnel' => 'Partenaire institutionnel',
    ];

    /**
     * Affiche la liste des partenaires PHOVE.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Récupérer le filtre de type
        $type = $request->input('type');

        // 2. Construire la requête de base — uniquement les partenaires actifs
        $query = Partenaire::query()
            ->where('statut', 'actif')
            ->withCount('projets');

        // 3. Appliquer le filtre de type avec validation
        if ($type && in_array($type, array_keys($this->types))) {
            $query->where('type', $type);
        }

        // 4. Trier par nom alphabétique
        $query->orderBy('nom');

        // 5. Récupérer tous les partenaires actifs
        $partenaires = $query->get();

        // 6. Compter par type pour les filtres
        $statsTypes = [
            'international' => Partenaire::where('statut', 'actif')->where('type', 'international')->count(),
            'institutionnel' => Partenaire::where('statut', 'actif')->where('type', 'institutionnel')->count(),
            'bailleur_fonds' => Partenaire::where('statut', 'actif')->where('type', 'bailleur_fonds')->count(),
            'technique' => Partenaire::where('statut', 'actif')->where('type', 'technique')->count(),
            'local' => Partenaire::where('statut', 'actif')->where('type', 'local')->count(),
        ];

        // 7. Statistiques globales
        $totalPartenaires = Partenaire::where('statut', 'actif')->count();
        $totalProjetsAssocies = Partenaire::where('statut', 'actif')
            ->withCount('projets')
            ->get()
            ->sum('projets_count');

        // 8. Types pour le filtre
        $types = $this->types;

        return view('public.partenaires', compact(
            'partenaires',
            'types',
            'statsTypes',
            'totalPartenaires',
            'totalProjetsAssocies',
            'type'
        ));
    }
}