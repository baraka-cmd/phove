<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PartenaireController extends Controller
{
    /**
     * Nombre de partenaires par page.
     *
     * @var int
     */
    protected int $perPage = 15;

    /**
     * Types de partenaires.
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
     * Statuts des partenaires.
     *
     * @var array<string, string>
     */
    protected array $statuts = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
    ];

    /**
     * Affiche la liste paginée et filtrée des partenaires PHOVE.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Partenaire::withCount('projets');

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche (nom, sigle, description, email)
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('sigle', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('email', 'like', '%' . $recherche . '%');
            });
        }

        $partenaires = $query->orderBy('nom')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalPartenaires = Partenaire::count();
        $partenairesActifs = Partenaire::where('statut', 'actif')->count();
        $partenairesInternationaux = Partenaire::where('statut', 'actif')->where('type', 'international')->count();
        $totalCollaborations = DB::table('projet_partenaire')->count();

        return view('admin.partenaires.index', compact(
            'partenaires',
            'totalPartenaires',
            'partenairesActifs',
            'partenairesInternationaux',
            'totalCollaborations'
        ));
    }

    /**
     * Affiche le formulaire de création d'un partenaire.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        $types = $this->types;
        return view('admin.partenaires.create', compact('projets', 'types'));
    }

    /**
     * Enregistre un nouveau partenaire.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'sigle' => ['nullable', 'string', 'max:20', 'unique:partenaires,sigle'],
            'type' => ['required', 'in:bailleur_fonds,technique,local,international,institutionnel'],
            'email' => ['nullable', 'email', 'max:191'],
            'telephone' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9\s\-\(\)]+$/'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'statut' => ['required', 'in:actif,inactif'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'],
            'projet_ids' => ['nullable', 'array'],
            'projet_ids.*' => ['exists:projets,id'],
        ], [
            'nom.required' => 'Le nom du partenaire est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'sigle.unique' => 'Ce sigle est déjà utilisé par un autre partenaire.',
            'type.required' => 'Le type de partenaire est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'telephone.regex' => 'Veuillez saisir un numéro de téléphone valide.',
            'site_web.url' => 'Veuillez saisir une URL valide (commençant par https://).',
            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ]);

        // Upload logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partenaires/logos', 'public');
        }

        $partenaire = Partenaire::create($validated);

        // Associer aux projets avec contribution
        if ($request->has('projet_ids')) {
            foreach ($request->projet_ids as $projetId) {
                $partenaire->projets()->attach($projetId, [
                    'role' => $validated['type'],
                    'date_debut' => $validated['date_debut'] ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.partenaires.index')
            ->with('success', 'Le partenaire « ' . $partenaire->nom . ' » a été ajouté avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un partenaire.
     *
     * @param Partenaire $partenaire
     * @return \Illuminate\View\View
     */
    public function show(Partenaire $partenaire)
    {
        $partenaire->load('projets');
        return view('admin.partenaires.show', compact('partenaire'));
    }

    /**
     * Affiche le formulaire de modification d'un partenaire.
     *
     * @param Partenaire $partenaire
     * @return \Illuminate\View\View
     */
    public function edit(Partenaire $partenaire)
    {
        $projets = Projet::orderBy('titre')->get();
        $types = $this->types;
        $statuts = $this->statuts;
        $partenaire->load('projets');
        return view('admin.partenaires.edit', compact('partenaire', 'projets', 'types', 'statuts'));
    }

    /**
     * Met à jour un partenaire.
     *
     * @param Request $request
     * @param Partenaire $partenaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Partenaire $partenaire)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'sigle' => ['nullable', 'string', 'max:20', Rule::unique('partenaires', 'sigle')->ignore($partenaire->id)],
            'type' => ['required', 'in:bailleur_fonds,technique,local,international,institutionnel'],
            'email' => ['nullable', 'email', 'max:191'],
            'telephone' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9\s\-\(\)]+$/'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'statut' => ['required', 'in:actif,inactif'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'],
            'projet_ids' => ['nullable', 'array'],
            'projet_ids.*' => ['exists:projets,id'],
        ], [
            'nom.required' => 'Le nom du partenaire est obligatoire.',
            'sigle.unique' => 'Ce sigle est déjà utilisé par un autre partenaire.',
            'type.required' => 'Le type de partenaire est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'telephone.regex' => 'Veuillez saisir un numéro de téléphone valide.',
            'site_web.url' => 'Veuillez saisir une URL valide (commençant par https://).',
            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ]);

        // Upload logo
        if ($request->hasFile('logo')) {
            if ($partenaire->logo) {
                Storage::disk('public')->delete($partenaire->logo);
            }
            $validated['logo'] = $request->file('logo')->store('partenaires/logos', 'public');
        }

        $partenaire->update($validated);

        // Synchroniser les projets avec les pivots
        if ($request->has('projet_ids')) {
            $syncData = [];
            foreach ($request->projet_ids as $projetId) {
                $syncData[$projetId] = [
                    'role' => $validated['type'],
                    'date_debut' => $validated['date_debut'] ?? now(),
                    'updated_at' => now(),
                ];
            }
            $partenaire->projets()->sync($syncData);
        } else {
            $partenaire->projets()->detach();
        }

        return redirect()->route('admin.partenaires.index')
            ->with('success', 'Le partenaire « ' . $partenaire->nom . ' » a été mis à jour avec succès.');
    }

    /**
     * Supprime un partenaire.
     *
     * @param Partenaire $partenaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Partenaire $partenaire)
    {
        // Vérifier si le partenaire a des projets associés
        $nombreProjets = $partenaire->projets()->count();

        // Supprimer le logo
        if ($partenaire->logo) {
            if (Storage::disk('public')->exists($partenaire->logo)) {
                Storage::disk('public')->delete($partenaire->logo);
            }
        }

        // Détacher les projets
        $partenaire->projets()->detach();

        // Supprimer le partenaire
        $partenaire->delete();

        $message = 'Le partenaire « ' . $partenaire->nom . ' » a été supprimé définitivement.';
        if ($nombreProjets > 0) {
            $message .= ' ' . $nombreProjets . ' association(s) projet(s) supprimée(s).';
        }

        return redirect()->route('admin.partenaires.index')
            ->with('success', $message);
    }
}