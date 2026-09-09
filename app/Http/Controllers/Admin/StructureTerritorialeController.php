<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StructureTerritoriale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StructureTerritorialeController extends Controller
{
    /**
     * Types de structures disponibles.
     *
     * @var array<string, string>
     */
    protected array $types = [
        'station' => 'Station (Pays)',
        'base' => 'Base (Province)',
        'antenne' => 'Antenne (Territoire/Commune)',
    ];

    /**
     * Statuts disponibles.
     *
     * @var array<string, string>
     */
    protected array $statuts = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
    ];

    /**
     * Affiche la liste paginée et filtrée des structures territoriales.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = StructureTerritoriale::with('parent')
            ->withCount(['enfants', 'organes']);

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
                  ->orWhere('pays', 'like', '%' . $recherche . '%')
                  ->orWhere('province', 'like', '%' . $recherche . '%')
                  ->orWhere('territoire', 'like', '%' . $recherche . '%');
            });
        }

        $structures = $query->orderBy('type')
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        // Statistiques
        $totalStructures = StructureTerritoriale::count();
        $stations = StructureTerritoriale::where('type', 'station')->count();
        $bases = StructureTerritoriale::where('type', 'base')->count();
        $antennes = StructureTerritoriale::where('type', 'antenne')->count();

        return view('admin.structures.index', compact(
            'structures',
            'totalStructures',
            'stations',
            'bases',
            'antennes'
        ));
    }

    /**
     * Affiche le formulaire de création d'une structure.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $parents = StructureTerritoriale::where('statut', 'actif')
            ->orderBy('type')
            ->orderBy('nom')
            ->get();

        return view('admin.structures.create', [
            'types' => $this->types,
            'statuts' => $this->statuts,
            'parents' => $parents,
        ]);
    }

    /**
     * Enregistre une nouvelle structure territoriale.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:station,base,antenne'],
            'pays' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'territoire' => ['nullable', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'exists:structures_territoriales,id'],
            'statut' => ['required', 'in:actif,inactif'],
        ], [
            'nom.required' => 'Le nom de la structure est obligatoire.',
            'type.required' => 'Le type de structure est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
        ]);

        $structure = StructureTerritoriale::create($validated);

        return redirect()->route('admin.structures.index')
            ->with('success', 'La structure « ' . $structure->nom . ' » a été créée avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'une structure.
     *
     * @param StructureTerritoriale $structure
     * @return \Illuminate\View\View
     */
    public function show(StructureTerritoriale $structure)
    {
        $structure->load([
            'parent',
            'enfants',
            'organes' => function ($query) {
                $query->where('actif', true)->with('membre')->orderBy('ordre');
            },
        ]);

        return view('admin.structures.show', compact('structure'));
    }

    /**
     * Affiche le formulaire de modification d'une structure.
     *
     * @param StructureTerritoriale $structure
     * @return \Illuminate\View\View
     */
    public function edit(StructureTerritoriale $structure)
    {
        $parents = StructureTerritoriale::where('statut', 'actif')
            ->where('id', '!=', $structure->id)
            ->orderBy('type')
            ->orderBy('nom')
            ->get();

        return view('admin.structures.edit', [
            'structure' => $structure,
            'types' => $this->types,
            'statuts' => $this->statuts,
            'parents' => $parents,
        ]);
    }

    /**
     * Met à jour une structure territoriale.
     *
     * @param Request $request
     * @param StructureTerritoriale $structure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, StructureTerritoriale $structure)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:station,base,antenne'],
            'pays' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'territoire' => ['nullable', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'exists:structures_territoriales,id', Rule::notIn([$structure->id])],
            'statut' => ['required', 'in:actif,inactif'],
        ], [
            'nom.required' => 'Le nom de la structure est obligatoire.',
            'type.required' => 'Le type de structure est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'parent_id.not_in' => 'Une structure ne peut pas être sa propre parente.',
        ]);

        $structure->update($validated);

        return redirect()->route('admin.structures.index')
            ->with('success', 'La structure « ' . $structure->nom . ' » a été mise à jour avec succès.');
    }

    /**
     * Supprime une structure territoriale.
     *
     * @param StructureTerritoriale $structure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(StructureTerritoriale $structure)
    {
        // Vérifier si la structure a des enfants
        $nombreEnfants = $structure->enfants()->count();

        if ($nombreEnfants > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer : cette structure a ' . $nombreEnfants . ' structure(s) enfant(s). Supprimez-les d\'abord.');
        }

        // Vérifier si la structure a des organes
        $nombreOrganes = $structure->organes()->count();

        // Détacher les organes
        $structure->organes()->update(['structure_id' => null]);

        $structure->delete();

        $message = 'La structure « ' . $structure->nom . ' » a été supprimée.';
        if ($nombreOrganes > 0) {
            $message .= ' ' . $nombreOrganes . ' poste(s) détaché(s).';
        }

        return redirect()->route('admin.structures.index')
            ->with('success', $message);
    }
}