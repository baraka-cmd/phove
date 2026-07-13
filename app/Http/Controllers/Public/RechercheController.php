<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Beneficiaire;
use App\Models\Document;
use App\Models\Projet;
use Illuminate\Http\Request;

class RechercheController extends Controller
{
    /**
     * Nombre de résultats par page pour chaque type.
     */
    protected int $perPage = 9;

    /**
     * Table de correspondance des domaines (français → valeur base).
     */
    protected array $domainesMap = [
        'santé' => 'sante_nutrition',
        'sante' => 'sante_nutrition',
        'nutrition' => 'sante_nutrition',
        'santé-nutrition' => 'sante_nutrition',
        'sante-nutrition' => 'sante_nutrition',
        'sante_nutrition' => 'sante_nutrition',
        'éducation' => 'education',
        'education' => 'education',
        'protection' => 'protection',
        'eau' => 'wash',
        'hygiène' => 'wash',
        'hygiene' => 'wash',
        'assainissement' => 'wash',
        'wash' => 'wash',
        'sécurité alimentaire' => 'securite_alimentaire',
        'sécurité' => 'securite_alimentaire',
        'securite alimentaire' => 'securite_alimentaire',
        'securite' => 'securite_alimentaire',
        'alimentaire' => 'securite_alimentaire',
        'communication' => 'communication',
        'environnement' => 'environnement',
        'paix' => 'paix',
        'genre' => 'genre',
    ];

    /**
     * Traite la recherche globale.
     */
    public function index(Request $request)
    {
        // 1. Récupérer le terme de recherche
        $q = $request->input('q', '');
        $q = trim($q);

        // 2. Si recherche vide, afficher la page sans résultats
        if (empty($q)) {
            return view('public.recherche', [
                'q' => '',
                'totalResultats' => 0,
                'projets' => collect(),
                'articles' => collect(),
                'documents' => collect(),
                'temoignages' => collect(),
            ]);
        }

        // 3. Trouver le domaine correspondant au terme recherché
        $qLower = mb_strtolower($q);
        $domaineRecherche = null;

        // Vérifier d'abord les correspondances exactes (ex: "sécurité alimentaire")
        if (isset($this->domainesMap[$qLower])) {
            $domaineRecherche = $this->domainesMap[$qLower];
        } else {
            // Vérifier les correspondances partielles (ex: "santé" dans "Santé-Nutrition")
            foreach ($this->domainesMap as $terme => $domaine) {
                if (str_contains($qLower, $terme) || str_contains($terme, $qLower)) {
                    $domaineRecherche = $domaine;
                    break;
                }
            }
        }

        // 4. Recherche dans les projets
        $projets = Projet::where('publier', true)
            ->where(function ($query) use ($q, $domaineRecherche) {
                $query->where('titre', 'like', '%' . $q . '%')
                      ->orWhere('description', 'like', '%' . $q . '%')
                      ->orWhere('zone_geographique', 'like', '%' . $q . '%')
                      ->orWhere('objectifs', 'like', '%' . $q . '%')
                      ->orWhere('bailleur', 'like', '%' . $q . '%')
                      ->orWhere('source_financement', 'like', '%' . $q . '%')
                      ->orWhere('province', 'like', '%' . $q . '%');

                // Ajouter la recherche par domaine si trouvé
                if ($domaineRecherche) {
                    $query->orWhere('domaine', $domaineRecherche);
                }
            })
            ->orderBy('date_debut', 'desc')
            ->paginate($this->perPage, ['*'], 'projets_page')
            ->appends(['q' => $q]);

        // 5. Recherche dans les articles
        $articles = Article::where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->where(function ($query) use ($q) {
                $query->where('titre', 'like', '%' . $q . '%')
                      ->orWhere('contenu', 'like', '%' . $q . '%')
                      ->orWhere('extrait', 'like', '%' . $q . '%');
            })
            ->with('auteur')
            ->orderBy('date_publication', 'desc')
            ->paginate($this->perPage, ['*'], 'articles_page')
            ->appends(['q' => $q]);

        // 6. Recherche dans les documents publics
        $documents = Document::where('public', true)
            ->where(function ($query) use ($q) {
                $query->where('titre', 'like', '%' . $q . '%')
                      ->orWhere('description', 'like', '%' . $q . '%');
            })
            ->orderBy('date_document', 'desc')
            ->paginate($this->perPage, ['*'], 'documents_page')
            ->appends(['q' => $q]);

        // 7. Recherche dans les témoignages publics
        $temoignages = Beneficiaire::where('temoignage_public', true)
            ->whereNotNull('temoignage')
            ->where(function ($query) use ($q) {
                $query->where('temoignage', 'like', '%' . $q . '%')
                      ->orWhere('nom', 'like', '%' . $q . '%')
                      ->orWhere('localisation', 'like', '%' . $q . '%');
            })
            ->with('projet')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, ['*'], 'temoignages_page')
            ->appends(['q' => $q]);

        // 8. Calculer le total des résultats
        $totalResultats = $projets->total() + $articles->total() + $documents->total() + $temoignages->total();

        return view('public.recherche', compact(
            'q',
            'totalResultats',
            'projets',
            'articles',
            'documents',
            'temoignages'
        ));
    }
}