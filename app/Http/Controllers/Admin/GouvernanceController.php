<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssembleeGenerale;
use App\Models\Membre;
use App\Models\Organe;
use App\Models\StructureTerritoriale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GouvernanceController extends Controller
{
    /**
     * Affiche la vue d'ensemble des organes de gouvernance PHOVE.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $organes = [
            'assemblee_generale',
            'conseil_administration',
            'commission_controle',
            'comite_suivi',
            'comite_gestion',
            'direction_executive',
        ];

        $stats = [];

        foreach ($organes as $organe) {
            $stats[$organe] = [
                'total' => Organe::where('organe', $organe)->count(),
                'actifs' => Organe::where('organe', $organe)->where('actif', true)->count(),
                'membres' => Organe::where('organe', $organe)
                    ->where('actif', true)
                    ->with('membre')
                    ->orderBy('ordre')
                    ->get(),
            ];
        }

        // Structures territoriales
        $stations = StructureTerritoriale::where('type', 'station')->where('statut', 'actif')->count();
        $bases = StructureTerritoriale::where('type', 'base')->where('statut', 'actif')->count();
        $antennes = StructureTerritoriale::where('type', 'antenne')->where('statut', 'actif')->count();

        // Dernière AG
        $derniereAG = AssembleeGenerale::where('statut', 'tenue')
            ->orderBy('date_reunion', 'desc')
            ->first();

        // Prochaine AG planifiée
        $prochaineAG = AssembleeGenerale::where('statut', 'planifiee')
            ->orderBy('date_reunion', 'asc')
            ->first();

        return view('admin.gouvernance.index', compact(
            'stats',
            'stations',
            'bases',
            'antennes',
            'derniereAG',
            'prochaineAG'
        ));
    }

    /**
     * Affiche le détail d'un organe spécifique.
     *
     * @param string $organe
     * @return \Illuminate\View\View
     */
    public function organe(string $organe)
    {
        $organesValides = [
            'assemblee_generale',
            'conseil_administration',
            'commission_controle',
            'comite_suivi',
            'comite_gestion',
            'direction_executive',
        ];

        if (!in_array($organe, $organesValides)) {
            abort(404);
        }

        $membres = Organe::where('organe', $organe)
            ->with(['membre', 'structure'])
            ->orderBy('actif', 'desc')
            ->orderBy('ordre')
            ->get();

        $membresDisponibles = Membre::where('statut', 'actif')->orderBy('nom')->get();
        $structures = StructureTerritoriale::where('statut', 'actif')->orderBy('nom')->get();
        $niveaux = [
            'international' => 'International',
            'national' => 'National',
            'provincial' => 'Provincial',
            'local' => 'Local',
        ];

        $organeLibelle = match ($organe) {
            'assemblee_generale' => 'Assemblée Générale',
            'conseil_administration' => 'Conseil d\'Administration',
            'commission_controle' => 'Commission de Contrôle',
            'comite_suivi' => 'Comité de Suivi',
            'comite_gestion' => 'Comité de Gestion',
            'direction_executive' => 'Direction Générale',
            default => $organe,
        };

        return view('admin.gouvernance.organe', compact(
            'organe',
            'organeLibelle',
            'membres',
            'membresDisponibles',
            'structures',
            'niveaux'
        ));
    }

    /**
 * Ajoute un membre à un organe.
 *
 * @param  Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function storeMembre(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'membre_id'    => ['required', 'exists:membres,id'],
        'organe'       => ['required', 'in:assemblee_generale,conseil_administration,commission_controle,comite_suivi,comite_gestion,direction_executive'],
        'poste'        => ['required', 'string', 'max:255'],
        'structure_id' => ['nullable', 'exists:structures_territoriales,id'],
        'niveau'       => ['nullable', 'in:international,national,provincial,local'],
        'date_debut'   => ['required', 'date'],
        'date_fin'     => ['nullable', 'date', 'after_or_equal:date_debut'],
        'ordre'        => ['nullable', 'integer', 'min:0'],
        'photo'        => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
    ], [
        'membre_id.required'    => 'Veuillez sélectionner un membre.',
        'membre_id.exists'      => 'Le membre sélectionné n\'existe pas.',
        'organe.required'       => 'L\'organe est obligatoire.',
        'organe.in'             => 'L\'organe sélectionné n\'est pas valide.',
        'poste.required'        => 'Le poste est obligatoire.',
        'date_debut.required'   => 'La date de début est obligatoire.',
        'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        'photo.image'           => 'Le fichier doit être une image.',
        'photo.mimes'           => 'La photo doit être au format JPG, PNG ou WebP.',
        'photo.max'             => 'La photo ne doit pas dépasser 2 Mo.',
    ]);

    // ─────────────────────────────────────────────────────────
    // 1. Vérifier que le membre n'est pas déjà dans cet organe
    // ─────────────────────────────────────────────────────────
    $dejaMembre = Organe::where('membre_id', $validated['membre_id'])
        ->where('organe', $validated['organe'])
        ->where('actif', true)
        ->exists();

    if ($dejaMembre) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Ce membre fait déjà partie de cet organe (mandat actif).');
    }

    // ─────────────────────────────────────────────────────────
    // 2. Gestion de la photo (uploadée sur le membre)
    // ─────────────────────────────────────────────────────────
    $membre = Membre::find($validated['membre_id']);

    if ($request->hasFile('photo') && $membre) {
        // Supprimer l'ancienne photo si elle existe
        if ($membre->photo && Storage::disk('public')->exists($membre->photo)) {
            Storage::disk('public')->delete($membre->photo);
        }

        // Stocker la nouvelle
        $photoPath = $request->file('photo')->store('membres/photos', 'public');
        $membre->update(['photo' => $photoPath]);
    }

    // ─────────────────────────────────────────────────────────
    // 3. Créer la liaison organe ↔ membre
    // ─────────────────────────────────────────────────────────
    Organe::create([
        'membre_id'    => $validated['membre_id'],
        'organe'       => $validated['organe'],
        'poste'        => $validated['poste'],
        'structure_id' => $validated['structure_id'] ?? null,
        'niveau'       => $validated['niveau'] ?? null,
        'date_debut'   => $validated['date_debut'],
        'date_fin'     => $validated['date_fin'] ?? null,
        'actif'        => true,
        'ordre'        => $validated['ordre'] ?? 0,
    ]);

    return redirect()->back()
        ->with('success', 'Le membre a été ajouté à l\'organe avec succès.');
}


/**
 * Met à jour un poste dans un organe.
 *
 * @param  Request  $request
 * @param  Organe   $organe
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateMembre(Request $request, Organe $organe): RedirectResponse
{
    $validated = $request->validate([
        'poste'        => ['required', 'string', 'max:255'],
        'structure_id' => ['nullable', 'exists:structures_territoriales,id'],
        'niveau'       => ['nullable', 'in:international,national,provincial,local'],
        'date_debut'   => ['required', 'date'],
        'date_fin'     => ['nullable', 'date', 'after_or_equal:date_debut'],
        'actif'        => ['nullable', 'boolean'],
        'ordre'        => ['nullable', 'integer', 'min:0'],
        'photo'        => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
    ], [
        'poste.required'          => 'Le poste est obligatoire.',
        'date_debut.required'     => 'La date de début est obligatoire.',
        'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        'photo.image'             => 'Le fichier doit être une image.',
        'photo.mimes'             => 'La photo doit être au format JPG, PNG ou WebP.',
        'photo.max'               => 'La photo ne doit pas dépasser 2 Mo.',
    ]);

    // ─────────────────────────────────────────────────────────
    // 1. Gestion de la photo (si uploadée)
    // ─────────────────────────────────────────────────────────
    if ($request->hasFile('photo') && $organe->membre) {
        $membre = $organe->membre;

        // Supprimer l'ancienne
        if ($membre->photo && Storage::disk('public')->exists($membre->photo)) {
            Storage::disk('public')->delete($membre->photo);
        }

        // Stocker la nouvelle
        $photoPath = $request->file('photo')->store('membres/photos', 'public');
        $membre->update(['photo' => $photoPath]);
    }

    // ─────────────────────────────────────────────────────────
    // 2. Mise à jour des infos du poste
    // ─────────────────────────────────────────────────────────
    $organe->update([
        'poste'        => $validated['poste'],
        'structure_id' => $validated['structure_id'] ?? $organe->structure_id,
        'niveau'       => $validated['niveau'] ?? $organe->niveau,
        'date_debut'   => $validated['date_debut'],
        'date_fin'     => $validated['date_fin'] ?? null,
        'actif'        => $request->boolean('actif', $organe->actif),
        'ordre'        => $validated['ordre'] ?? $organe->ordre,
    ]);

    return redirect()->back()
        ->with('success', 'Le poste a été mis à jour avec succès.');
}

    /**
     * Retire un membre d'un organe (désactive le mandat).
     *
     * @param Organe $organe
     * @return \Illuminate\Http\RedirectResponse
     */
    public function retirerMembre(Organe $organe)
    {
        $organe->update([
            'actif' => false,
            'date_fin' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Le membre a été retiré de l\'organe.');
    }

    /**
     * Affiche la liste des Assemblées Générales.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function assemblies(Request $request)
    {
        $query = AssembleeGenerale::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('annee')) {
            $query->whereYear('date_reunion', $request->annee);
        }

        $assemblies = $query->orderBy('date_reunion', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => AssembleeGenerale::count(),
            'tenues' => AssembleeGenerale::where('statut', 'tenue')->count(),
            'planifiees' => AssembleeGenerale::where('statut', 'planifiee')->count(),
        ];

        $annees = AssembleeGenerale::selectRaw('YEAR(date_reunion) as annee')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        return view('admin.gouvernance.assemblies', compact('assemblies', 'stats', 'annees'));
    }

    /**
     * Enregistre une nouvelle Assemblée Générale.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAssembly(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:ordinaire,extraordinaire'],
            'date_reunion' => ['required', 'date'],
            'lieu' => ['required', 'string', 'max:255'],
            'ordre_du_jour' => ['nullable', 'string'],
            'membres_total' => ['nullable', 'integer', 'min:0'],
            'membres_presents' => ['nullable', 'integer', 'min:0'],
            'membres_absents' => ['nullable', 'integer', 'min:0'],
            'decisions' => ['nullable', 'string'],
            'resolutions' => ['nullable', 'string'],
            'statut' => ['required', 'in:planifiee,tenue,annulee,reportee'],
            'pv_fichier' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'type.required' => 'Le type d\'AG est obligatoire.',
            'date_reunion.required' => 'La date de réunion est obligatoire.',
            'lieu.required' => 'Le lieu est obligatoire.',
            'pv_fichier.mimes' => 'Le PV doit être au format PDF.',
            'pv_fichier.max' => 'Le PV ne doit pas dépasser 10 Mo.',
        ]);

        if ($request->hasFile('pv_fichier')) {
            $validated['pv_fichier'] = $request->file('pv_fichier')->store('assemblees/pv', 'public');
        }

        if (!empty($validated['membres_total']) && !empty($validated['membres_presents'])) {
            $validated['quorum_atteint'] = $validated['membres_presents'] >= ($validated['membres_total'] / 2);
        }

        AssembleeGenerale::create($validated);

        return redirect()->route('admin.gouvernance.assemblies')
            ->with('success', 'L\'Assemblée Générale a été enregistrée avec succès.');
    }

    /**
     * Met à jour une Assemblée Générale.
     *
     * @param Request $request
     * @param AssembleeGenerale $assembly
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAssembly(Request $request, AssembleeGenerale $assembly)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:ordinaire,extraordinaire'],
            'date_reunion' => ['required', 'date'],
            'lieu' => ['required', 'string', 'max:255'],
            'ordre_du_jour' => ['nullable', 'string'],
            'membres_total' => ['nullable', 'integer', 'min:0'],
            'membres_presents' => ['nullable', 'integer', 'min:0'],
            'membres_absents' => ['nullable', 'integer', 'min:0'],
            'decisions' => ['nullable', 'string'],
            'resolutions' => ['nullable', 'string'],
            'statut' => ['required', 'in:planifiee,tenue,annulee,reportee'],
            'pv_fichier' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        if ($request->hasFile('pv_fichier')) {
            if ($assembly->pv_fichier) {
                Storage::disk('public')->delete($assembly->pv_fichier);
            }
            $validated['pv_fichier'] = $request->file('pv_fichier')->store('assemblees/pv', 'public');
        }

        if (!empty($validated['membres_total']) && !empty($validated['membres_presents'])) {
            $validated['quorum_atteint'] = $validated['membres_presents'] >= ($validated['membres_total'] / 2);
        }

        $assembly->update($validated);

        return redirect()->route('admin.gouvernance.assemblies')
            ->with('success', 'L\'Assemblée Générale a été mise à jour avec succès.');
    }

    /**
     * Affiche le formulaire de création d'une AG.
     *
     * @return \Illuminate\View\View
     */
    public function createAssembly()
    {
        return view('admin.gouvernance.assembly-create');
    }

    /**
     * Affiche le formulaire de modification d'une AG.
     *
     * @param AssembleeGenerale $assembly
     * @return \Illuminate\View\View
     */
    public function editAssembly(AssembleeGenerale $assembly)
    {
        return view('admin.gouvernance.assembly-edit', compact('assembly'));
    }

    /**
     * Supprime une AG.
     *
     * @param AssembleeGenerale $assembly
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAssembly(AssembleeGenerale $assembly)
    {
        if ($assembly->pv_fichier) {
            Storage::disk('public')->delete($assembly->pv_fichier);
        }
        $assembly->delete();
        return redirect()->route('admin.gouvernance.assemblies')
            ->with('success', 'L\'Assemblée Générale a été supprimée.');
    }

    /**
     * Affiche le détail d'une Assemblée Générale.
     *
     * @param AssembleeGenerale $assembly
     * @return \Illuminate\View\View
     */
    public function showAssembly(AssembleeGenerale $assembly)
    {
        return view('admin.gouvernance.assembly-show', compact('assembly'));
    }
}