<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PartenaireController extends Controller
{
    /**
     * Types de partenaires disponibles.
     *
     * @var array<string, string>
     */
    protected array $types = [
        'bailleur_fonds' => 'Bailleur de fonds',
        'technique'      => 'Partenaire technique',
        'local'          => 'Partenaire local',
        'international'  => 'Partenaire international',
        'institutionnel' => 'Partenaire institutionnel',
    ];

    /**
     * Affiche la liste des partenaires PHOVE.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        // ---------------------------------------------------------------------
        // 1. Filtre de type (validé)
        // ---------------------------------------------------------------------
        $type = (string) $request->input('type', '');

        if ($type !== '' && !array_key_exists($type, $this->types)) {
            $type = '';
        }

        // ---------------------------------------------------------------------
        // 2. Requête de base — partenaires actifs
        // ---------------------------------------------------------------------
        $query = Partenaire::query()
            ->where('statut', 'actif')
            ->with([
                // On charge uniquement les projets publiés, avec les champs utiles
                'projets' => function ($q) {
                    $q->select('projets.id', 'projets.titre', 'projets.code_projet', 'projets.slug')
                      ->where('projets.publier', true);
                },
            ])
            ->withCount([
                // Comptage des projets publiés uniquement
                'projets' => function ($q) {
                    $q->where('projets.publier', true);
                },
            ]);

        // ---------------------------------------------------------------------
        // 3. Appliquer le filtre de type
        // ---------------------------------------------------------------------
        if ($type !== '') {
            $query->where('type', $type);
        }

        // ---------------------------------------------------------------------
        // 4. Tri alphabétique + pagination
        // ---------------------------------------------------------------------
        $partenaires = $query->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        // ---------------------------------------------------------------------
        // 5. Statistiques par type (1 seule requête SQL au lieu de 5)
        // ---------------------------------------------------------------------
        $statsTypes = $this->recupererStatsTypes();

        // ---------------------------------------------------------------------
        // 6. Statistiques globales
        // ---------------------------------------------------------------------
        $totalPartenaires = array_sum(array_values($statsTypes));

        $totalProjetsAssocies = (int) DB::table('projet_partenaire')
            ->join('partenaires', 'partenaires.id', '=', 'projet_partenaire.partenaire_id')
            ->join('projets', 'projets.id', '=', 'projet_partenaire.projet_id')
            ->where('partenaires.statut', 'actif')
            ->where('projets.publier', true)
            ->distinct()
            ->count('projet_partenaire.projet_id');

        // ---------------------------------------------------------------------
        // 7. Types pour le filtre
        // ---------------------------------------------------------------------
        $types = $this->types;

        // ---------------------------------------------------------------------
        // 8. Vue
        // ---------------------------------------------------------------------
        return view('public.partenaires', [
            'partenaires'          => $partenaires,
            'types'                => $types,
            'statsTypes'           => $statsTypes,
            'totalPartenaires'     => $totalPartenaires,
            'totalProjetsAssocies' => $totalProjetsAssocies,
            'type'                 => $type,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère le nombre de partenaires actifs par type
     * en une seule requête SQL.
     *
     * @return array<string, int>
     */
    protected function recupererStatsTypes(): array
    {
        $raw = Partenaire::query()
            ->where('statut', 'actif')
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // On garantit que toutes les clés existent (même à 0)
        $stats = [];
        foreach (array_keys($this->types) as $key) {
            $stats[$key] = (int) ($raw[$key] ?? 0);
        }

        return $stats;
    }
}