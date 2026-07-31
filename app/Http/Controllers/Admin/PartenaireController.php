<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        'technique' => 'Partenaire technique',
        'local' => 'Partenaire local',
        'international' => 'Partenaire international',
        'institutionnel' => 'Partenaire institutionnel',
    ];

    /**
     * Affiche la liste paginée et filtrée des partenaires.
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

        // Recherche
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

        // Statistiques
        $totalPartenaires = Partenaire::count();
        $partenairesActifs = Partenaire::where('statut', 'actif')->count();
        $totalCollaborations = \DB::table('projet_partenaire')->count();

        return view('admin.partenaires.index', compact(
            'partenaires',
            'totalPartenaires',
            'partenairesActifs',
            'totalCollaborations'
        ));
    }

    /**
     * Affiche le formulaire de création d'un partenaire.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.partenaires.create', compact('projets'));
    }

    /**
     * Enregistre un nouveau partenaire.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bailleur_fonds,technique,local,international,institutionnel'],
            'email' => ['nullable', 'email', 'max:191'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'statut' => ['required', 'in:actif,inactif'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'projet_ids' => ['nullable', 'array'],
            'projet_ids.*' => ['exists:projets,id'],
        ], [
            'nom.required' => 'Le nom du partenaire est obligatoire.',
            'type.required' => 'Le type de partenaire est obligatoire.',
        ]);

        // Upload logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partenaires/logos', 'public');
        }

        $partenaire = Partenaire::create($validated);

        // Associer aux projets
        if ($request->has('projet_ids')) {
            $partenaire->projets()->attach($request->projet_ids, [
                'role' => $validated['type'],
                'date_debut' => $validated['date_debut'] ?? now(),
            ]);
        }

        return redirect()->route('admin.partenaires.index')
            ->with('success', 'Le partenaire a été ajouté avec succès.');
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
     * Affiche le formulaire de modification d'un partenaire.
     */
    public function edit(Partenaire $partenaire)
    {
        $projets = Projet::orderBy('titre')->get();
        $partenaire->load('projets');
        return view('admin.partenaires.edit', compact('partenaire', 'projets'));
    }

    /**
     * Met à jour un partenaire.
     */
    public function update(Request $request, Partenaire $partenaire)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bailleur_fonds,technique,local,international,institutionnel'],
            'email' => ['nullable', 'email', 'max:191'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'statut' => ['required', 'in:actif,inactif'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'projet_ids' => ['nullable', 'array'],
            'projet_ids.*' => ['exists:projets,id'],
        ]);

        // Upload logo
        if ($request->hasFile('logo')) {
            if ($partenaire->logo) {
                Storage::disk('public')->delete($partenaire->logo);
            }
            $validated['logo'] = $request->file('logo')->store('partenaires/logos', 'public');
        }

        $partenaire->update($validated);

        // Synchroniser les projets
        if ($request->has('projet_ids')) {
            $partenaire->projets()->sync($request->projet_ids);
        } else {
            $partenaire->projets()->detach();
        }

        return redirect()->route('admin.partenaires.index')
            ->with('success', 'Le partenaire a été mis à jour avec succès.');
    }

    /**
     * Supprime un partenaire.
     */
    public function destroy(Partenaire $partenaire)
    {
        if ($partenaire->logo) {
            Storage::disk('public')->delete($partenaire->logo);
        }

        // Détacher les projets
        $partenaire->projets()->detach();
        $partenaire->delete();

        return redirect()->route('admin.partenaires.index')
            ->with('success', 'Le partenaire a été supprimé définitivement.');
    }
}