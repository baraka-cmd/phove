<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;

class ProjetController extends Controller
{
    /**
     * Nombre de projets par page.
     *
     * @var int
     */
    protected int $perPage = 9;

    /**
     * Affiche la liste paginée et filtrée des projets PHOVE.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Récupérer les paramètres de filtre
        $domaine = $request->input('domaine');
        $zone = $request->input('zone');
        $statut = $request->input('statut');
        $annee = $request->input('annee');
        $recherche = $request->input('recherche');

        // 2. Construire la requête de base
        $query = Projet::query()->where('publier', true);

        // 3. Appliquer les filtres
        if ($domaine) {
            $query->where('domaine', $domaine);
        }

        if ($zone) {
            $query->where('zone_geographique', 'like', '%' . $zone . '%');
        }

        if ($statut) {
            $query->where('statut', $statut);
        }

        if ($annee) {
            $query->whereYear('date_debut', $annee);
        }

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('zone_geographique', 'like', '%' . $recherche . '%')
                  ->orWhere('code_projet', 'like', '%' . $recherche . '%');
            });
        }

        // 4. Trier par date de début (plus récent d'abord)
        $query->orderBy('date_debut', 'desc');

        // 5. Paginer les résultats
        $projets = $query->paginate($this->perPage)->withQueryString();

        // 6. Récupérer les zones distinctes pour les filtres
        $zones = Projet::where('publier', true)
            ->distinct()
            ->pluck('zone_geographique')
            ->map(function ($zone) {
                $parts = explode(',', $zone);
                return array_map('trim', $parts);
            })
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        // 7. Récupérer les années distinctes pour le filtre
        $annees = Projet::where('publier', true)
            ->selectRaw('YEAR(date_debut) as annee')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        // 8. Compter les projets par zone pour les cartes de zones
        $zonesStats = [];
        foreach ($zones as $zoneName) {
            $zonesStats[$zoneName] = Projet::where('publier', true)
                ->where('zone_geographique', 'like', '%' . $zoneName . '%')
                ->count();
        }

        // 9. Statistiques globales
        $totalProjets = Projet::where('publier', true)->count();
        $projetsEnCours = Projet::where('publier', true)->where('statut', 'en_cours')->count();
        $projetsTermines = Projet::where('publier', true)->where('statut', 'termine')->count();
        $totalBudget = Projet::where('publier', true)->sum('budget_total');

        // 10. Domaines pour le filtre (adaptés PHOVE)
        $domaines = [
            'sante_nutrition' => 'Santé & Nutrition',
            'education' => 'Éducation & Formation',
            'protection' => 'Protection Sociale',
            'wash' => 'Eau & Assainissement',
            'securite_alimentaire' => 'Sécurité Alimentaire & Agriculture',
            'communication' => 'Communication',
            'environnement' => 'Environnement',
            'paix' => 'Paix & Cohésion',
            'genre' => 'Genre',
            'spirituel' => 'Accompagnement Spirituel',
            'infrastructure' => 'Infrastructures',
            'economique' => 'Activités Socio-Économiques',
            'judiciaire' => 'Assistance Judiciaire',
        ];

        // 11. Statuts pour le filtre
        $statuts = [
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'en_preparation' => 'En préparation',
            'suspendu' => 'Suspendu',
        ];

        return view('public.projets.index', compact(
            'projets',
            'zones',
            'zonesStats',
            'annees',
            'domaines',
            'statuts',
            'domaine',
            'zone',
            'statut',
            'annee',
            'recherche',
            'totalProjets',
            'projetsEnCours',
            'projetsTermines',
            'totalBudget'
        ));
    }

    /**
     * Affiche le détail d'un projet PHOVE.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        // 1. Trouver le projet par son slug
        $projet = Projet::where('slug', $slug)
            ->where('publier', true)
            ->firstOrFail();

        // 2. Charger les relations simples (sans limit)
        $projet->load([
            'indicateurs',
            'partenaires',
            'medias' => function ($query) {
                $query->where('publier', true)->orderBy('ordre');
            },
        ]);

        // 3. Charger les bénéficiaires séparément (contourne le bug MySQL)
        $beneficiaires = $projet->beneficiaires()
            ->where('temoignage_public', true)
            ->whereNotNull('temoignage')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $projet->setRelation('beneficiaires', $beneficiaires);

        // 4. Projets connexes (même domaine ou même zone)
        $projetsConnexes = Projet::where('publier', true)
            ->where('id', '!=', $projet->id)
            ->where(function ($query) use ($projet) {
                $query->where('domaine', $projet->domaine)
                    ->orWhere('zone_geographique', 'like', '%' . $projet->zone_geographique . '%');
            })
            ->orderBy('date_debut', 'desc')
            ->take(3)
            ->get();

        return view('public.projets.show', compact('projet', 'projetsConnexes'));
    }
}