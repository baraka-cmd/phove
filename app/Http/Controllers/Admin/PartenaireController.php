<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PartenaireController extends Controller
{
    /**
     * Nombre de partenaires par page.
     */
    protected int $perPage = 15;

    /**
     * Types de partenaires.
     */
    protected array $types = [
        'bailleur_fonds' => 'Bailleur de fonds',
        'technique'      => 'Partenaire technique',
        'local'          => 'Partenaire local',
        'international'  => 'Partenaire international',
        'institutionnel' => 'Partenaire institutionnel',
    ];

    /**
     * Statuts des partenaires.
     */
    protected array $statuts = [
        'actif'   => 'Actif',
        'inactif' => 'Inactif',
    ];

    /**
     * Règles de validation communes (store + update).
     */
    protected function validationRules(?Partenaire $partenaire = null): array
    {
        return [
            'nom'          => ['required', 'string', 'max:255'],
            'type'         => ['required', Rule::in(array_keys($this->types))],
            'email'        => ['nullable', 'email', 'max:191'],
            'telephone'    => ['nullable', 'string', 'max:20', 'regex:/^[+0-9\s\-\(\)]+$/'],
            'adresse'      => ['nullable', 'string', 'max:255'],
            'site_web'     => ['nullable', 'url', 'max:255'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'statut'       => ['required', Rule::in(array_keys($this->statuts))],
            'date_debut'   => ['nullable', 'date'],
            'date_fin'     => ['nullable', 'date', 'after_or_equal:date_debut'],
            'logo'         => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'projet_ids'   => ['nullable', 'array'],
            'projet_ids.*' => ['exists:projets,id'],
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    protected function validationMessages(): array
    {
        return [
            'nom.required'              => 'Le nom du partenaire est obligatoire.',
            'nom.max'                   => 'Le nom ne doit pas dépasser 255 caractères.',
            'type.required'             => 'Le type de partenaire est obligatoire.',
            'type.in'                   => 'Le type sélectionné n\'est pas valide.',
            'email.email'               => 'Veuillez saisir une adresse email valide.',
            'telephone.regex'           => 'Veuillez saisir un numéro de téléphone valide.',
            'telephone.max'             => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'site_web.url'              => 'Veuillez saisir une URL valide (commençant par https://).',
            'statut.required'           => 'Le statut est obligatoire.',
            'statut.in'                 => 'Le statut sélectionné n\'est pas valide.',
            'logo.image'                => 'Le logo doit être une image.',
            'logo.mimes'                => 'Le logo doit être au format JPG, PNG ou WebP.',
            'logo.max'                  => 'Le logo ne doit pas dépasser 2 Mo.',
            'date_fin.after_or_equal'   => 'La date de fin doit être postérieure ou égale à la date de début.',
            'projet_ids.*.exists'       => 'Un des projets sélectionnés n\'existe pas.',
        ];
    }

    /**
     * Affiche la liste paginée et filtrée des partenaires.
     */
    public function index(Request $request)
    {
        $query = Partenaire::withCount('projets');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('email', 'like', '%' . $recherche . '%');
            });
        }

        $partenaires = $query->orderBy('nom')
            ->paginate($this->perPage)
            ->withQueryString();

        $totalPartenaires          = Partenaire::count();
        $partenairesActifs         = Partenaire::where('statut', 'actif')->count();
        $partenairesInternationaux = Partenaire::where('statut', 'actif')
            ->where('type', 'international')
            ->count();
        $totalCollaborations       = DB::table('projet_partenaire')->count();

        return view('admin.partenaires.index', compact(
            'partenaires',
            'totalPartenaires',
            'partenairesActifs',
            'partenairesInternationaux',
            'totalCollaborations'
        ));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        $types   = $this->types;
        $statuts = $this->statuts;

        return view('admin.partenaires.create', compact('projets', 'types', 'statuts'));
    }

    /**
     * Enregistre un nouveau partenaire.
     */
    public function store(Request $request)
{
    $validated = $request->validate(
        $this->validationRules(),
        $this->validationMessages()
    );

    if ($request->hasFile('logo')) {
        $validated['logo'] = media_store($request->file('logo'), 'partenaires/logos');
    }

    $partenaire = Partenaire::create($validated);

    // Associer les projets
    if ($request->filled('projet_ids')) {
        $syncData = [];
        foreach ($request->projet_ids as $projetId) {
            $syncData[$projetId] = [
                'role'       => $this->roleFromType($validated['type']),
                'date_debut' => $validated['date_debut'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $partenaire->projets()->sync($syncData);
    }

    return redirect()->route('admin.partenaires.index')
        ->with('success', 'Le partenaire « ' . $partenaire->nom . ' » a été ajouté avec succès.');
}

    /**
     * Affiche la fiche détaillée d'un partenaire.
     */
    public function show(Partenaire $partenaire)
    {
        $partenaire->load('projets');

        return view('admin.partenaires.show', compact('partenaire'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Partenaire $partenaire)
    {
        $projets = Projet::orderBy('titre')->get();
        $types   = $this->types;
        $statuts = $this->statuts;

        $partenaire->load('projets');

        return view('admin.partenaires.edit', compact(
            'partenaire',
            'projets',
            'types',
            'statuts'
        ));
    }

    /**
     * Met à jour un partenaire.
     */
    public function update(Request $request, Partenaire $partenaire)
{
    $validated = $request->validate(
        $this->validationRules($partenaire),
        $this->validationMessages()
    );

    if ($request->hasFile('logo')) {
        if ($partenaire->logo) {
            media_delete($partenaire->logo);
        }
        $validated['logo'] = media_store($request->file('logo'), 'partenaires/logos');
    }

    $partenaire->update($validated);

    // Synchroniser les projets (avec gestion du "tout décocher")
    if ($request->has('projet_ids_present') || $request->filled('projet_ids')) {
        $syncData = [];
        if ($request->filled('projet_ids')) {
            foreach ($request->projet_ids as $projetId) {
                $syncData[$projetId] = [
                    'role'       => $this->roleFromType($validated['type']),
                    'date_debut' => $validated['date_debut'] ?? null,
                    'updated_at' => now(),
                ];
            }
        }
        $partenaire->projets()->sync($syncData);
    }

    return redirect()->route('admin.partenaires.index')
        ->with('success', 'Le partenaire « ' . $partenaire->nom . ' » a été mis à jour avec succès.');
}

    /**
     * Supprime un partenaire.
     */
    public function destroy(Partenaire $partenaire)
    {
        $nombreProjets = $partenaire->projets()->count();

        // Supprimer le logo
        if ($partenaire->logo) {
            media_delete($partenaire->logo);
        }

        // Détacher les projets
        $partenaire->projets()->detach();

        // Supprimer le partenaire
        $nom = $partenaire->nom;
        $partenaire->delete();

        $message = 'Le partenaire « ' . $nom . ' » a été supprimé définitivement.';
        if ($nombreProjets > 0) {
            $message .= ' ' . $nombreProjets . ' association(s) projet(s) supprimée(s).';
        }

        return redirect()->route('admin.partenaires.index')
            ->with('success', $message);
    }

   

/**
 * Mappe le type de partenaire vers une valeur valide de l'ENUM "role"
 * défini dans la table pivot projet_partenaire.
 */
protected function roleFromType(string $type): string
{
    return match($type) {
        'bailleur_fonds' => 'bailleur_principal',
        'technique'      => 'partenaire_technique',
        'local'          => 'partenaire_mise_en_oeuvre',
        'international'  => 'partenaire_institutionnel',
        'institutionnel' => 'partenaire_institutionnel',
        default          => 'partenaire_technique',
    };
}
}