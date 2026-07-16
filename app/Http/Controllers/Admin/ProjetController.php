<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjetController extends Controller
{
    /**
     * Nombre de projets par page.
     */
    protected int $perPage = 15;

    /**
     * Domaines d'intervention (Article 9 des Statuts).
     */
    protected array $domaines = [
        'sante_nutrition' => 'Santé-Nutrition',
        'education' => 'Éducation',
        'protection' => 'Protection de l\'enfant et de la femme',
        'wash' => 'Eau, Assainissement et Hygiène (WASH)',
        'securite_alimentaire' => 'Sécurité Alimentaire',
        'communication' => 'Communication',
        'environnement' => 'Protection de l\'environnement et développement durable',
        'paix' => 'Promotion de la Paix et transformation des conflits',
        'genre' => 'Sensibilité au genre',
    ];

    /**
     * Statuts des projets.
     */
    protected array $statuts = [
        'en_preparation' => 'En préparation',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'suspendu' => 'Suspendu',
    ];

    /**
     * Affiche la liste paginée et filtrée des projets.
     */
    public function index(Request $request)
    {
        $query = Projet::query();

        // Filtre par domaine
        if ($request->filled('domaine')) {
            $query->where('domaine', $request->domaine);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par zone
        if ($request->filled('zone')) {
            $query->where('zone_geographique', 'like', '%' . $request->zone . '%');
        }

        // Recherche textuelle
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('zone_geographique', 'like', '%' . $recherche . '%')
                  ->orWhere('bailleur', 'like', '%' . $recherche . '%');
            });
        }

        $projets = $query->orderBy('date_debut', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalProjets = Projet::count();
        $projetsEnCours = Projet::where('statut', 'en_cours')->count();
        $projetsTermines = Projet::where('statut', 'termine')->count();
        $projetsEnPreparation = Projet::where('statut', 'en_preparation')->count();

        // Zones distinctes pour le filtre
        $zones = Projet::select('zone_geographique')
            ->distinct()
            ->pluck('zone_geographique')
            ->map(fn($z) => trim($z))
            ->unique()
            ->sort()
            ->values();

        return view('admin.projets.index', compact(
            'projets',
            'totalProjets',
            'projetsEnCours',
            'projetsTermines',
            'projetsEnPreparation',
            'zones'
        ));
    }

    /**
     * Affiche le formulaire de création d'un projet.
     */
    public function create()
    {
        return view('admin.projets.create', [
            'domaines' => $this->domaines,
            'statuts' => $this->statuts,
        ]);
    }

    /**
     * Enregistre un nouveau projet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'domaine' => ['required', 'in:' . implode(',', array_keys($this->domaines))],
            'zone_geographique' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'budget_total' => ['nullable', 'numeric', 'min:0'],
            'budget_utilise' => ['nullable', 'numeric', 'min:0'],
            'bailleur' => ['nullable', 'string', 'max:255'],
            'source_financement' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:' . implode(',', array_keys($this->statuts))],
            'description' => ['required', 'string'],
            'objectifs' => ['nullable', 'string'],
            'resultats_attendus' => ['nullable', 'string'],
            'image_principale' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'publier' => ['nullable', 'boolean'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'domaine.required' => 'Le domaine est obligatoire.',
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            'statut.required' => 'Le statut est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'image_principale.image' => 'Le fichier doit être une image.',
            'image_principale.mimes' => 'L\'image doit être au format JPG, PNG ou WebP.',
            'image_principale.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);

        // Générer le slug
        $validated['slug'] = Str::slug($validated['titre']);

        // Gérer l'upload de l'image
        if ($request->hasFile('image_principale')) {
            $validated['image_principale'] = $request->file('image_principale')->store('projets/images', 'public');
        }

        $validated['publier'] = $request->boolean('publier');

        Projet::create($validated);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Le projet a été créé avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un projet (prévisualisation admin).
     */
    public function show(Projet $projet)
    {
        $projet->load(['indicateurs', 'partenaires', 'medias']);

        return view('admin.projets.show', compact('projet'));
    }

    /**
     * Affiche le formulaire de modification d'un projet.
     */
    public function edit(Projet $projet)
    {
        return view('admin.projets.edit', [
            'projet' => $projet,
            'domaines' => $this->domaines,
            'statuts' => $this->statuts,
        ]);
    }

    /**
     * Met à jour un projet.
     */
    public function update(Request $request, Projet $projet)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'domaine' => ['required', 'in:' . implode(',', array_keys($this->domaines))],
            'zone_geographique' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'budget_total' => ['nullable', 'numeric', 'min:0'],
            'budget_utilise' => ['nullable', 'numeric', 'min:0'],
            'bailleur' => ['nullable', 'string', 'max:255'],
            'source_financement' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:' . implode(',', array_keys($this->statuts))],
            'description' => ['required', 'string'],
            'objectifs' => ['nullable', 'string'],
            'resultats_attendus' => ['nullable', 'string'],
            'image_principale' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'publier' => ['nullable', 'boolean'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'domaine.required' => 'Le domaine est obligatoire.',
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
            'statut.required' => 'Le statut est obligatoire.',
            'description.required' => 'La description est obligatoire.',
        ]);

        // Mettre à jour le slug si le titre a changé
        if ($projet->titre !== $validated['titre']) {
            $validated['slug'] = Str::slug($validated['titre']);
        }

        // Gérer l'upload de l'image
        if ($request->hasFile('image_principale')) {
            // Supprimer l'ancienne image
            if ($projet->image_principale) {
                Storage::disk('public')->delete($projet->image_principale);
            }
            $validated['image_principale'] = $request->file('image_principale')->store('projets/images', 'public');
        }

        $validated['publier'] = $request->boolean('publier');

        $projet->update($validated);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Le projet a été mis à jour avec succès.');
    }

    /**
     * Supprime un projet.
     */
    public function destroy(Projet $projet)
    {
        // Supprimer l'image
        if ($projet->image_principale) {
            Storage::disk('public')->delete($projet->image_principale);
        }

        $projet->delete();

        return redirect()->route('admin.projets.index')
            ->with('success', 'Le projet a été supprimé définitivement.');
    }

    /**
     * Change le statut d'un projet rapidement.
     */
    public function changeStatus(Request $request, Projet $projet)
    {
        $request->validate([
            'statut' => ['required', 'in:en_cours,termine,suspendu,en_preparation'],
        ]);

        $projet->update(['statut' => $request->statut]);

        return redirect()->back()
            ->with('success', 'Le statut du projet a été changé en "' . $this->statuts[$request->statut] . '".');
    }

    /**
     * Active/désactive la publication d'un projet.
     */
    public function togglePublier(Projet $projet)
    {
        $projet->update(['publier' => !$projet->publier]);

        $etat = $projet->publier ? 'publié' : 'dépublié';

        return redirect()->back()
            ->with('success', 'Le projet a été ' . $etat . ' avec succès.');
    }
}