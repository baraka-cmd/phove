<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssembleeGenerale;
use App\Models\Membre;
use App\Models\Organe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GouvernanceController extends Controller
{
    /**
     * Affiche la vue d'ensemble des 4 organes de gouvernance.
     */
    public function index()
    {
        $organes = ['conseil_administration', 'direction_executive', 'commission_controle', 'assemblee_generale'];
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

        // Dernière AG
        $derniereAG = AssembleeGenerale::where('statut', 'tenue')
            ->orderBy('date_reunion', 'desc')
            ->first();

        // Prochaine AG planifiée
        $prochaineAG = AssembleeGenerale::where('statut', 'planifiee')
            ->orderBy('date_reunion', 'asc')
            ->first();

        return view('admin.gouvernance.index', compact('stats', 'derniereAG', 'prochaineAG'));
    }

    /**
     * Affiche le détail d'un organe spécifique.
     */
    public function organe(string $organe)
    {
        $organesValides = ['conseil_administration', 'direction_executive', 'commission_controle', 'assemblee_generale'];

        if (!in_array($organe, $organesValides)) {
            abort(404);
        }

        $membres = Organe::where('organe', $organe)
            ->with('membre')
            ->orderBy('actif', 'desc')
            ->orderBy('ordre')
            ->get();

        $membresDisponibles = Membre::where('statut', 'actif')->orderBy('nom')->get();

        $organeLibelle = match ($organe) {
            'conseil_administration' => 'Conseil d\'Administration',
            'direction_executive' => 'Direction Exécutive',
            'commission_controle' => 'Commission de Contrôle',
            'assemblee_generale' => 'Assemblée Générale',
            default => $organe,
        };

        return view('admin.gouvernance.organe', compact('organe', 'organeLibelle', 'membres', 'membresDisponibles'));
    }

    /**
     * Ajoute un membre à un organe.
     */
    public function storeMembre(Request $request)
    {
        
        $validated = $request->validate([
            'membre_id' => ['required', 'exists:membres,id'],
            'organe' => ['required', 'in:conseil_administration,direction_executive,commission_controle,assemblee_generale'],
            'poste' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ], [
            'membre_id.required' => 'Veuillez sélectionner un membre.',
            'organe.required' => 'L\'organe est obligatoire.',
            'poste.required' => 'Le poste est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        ]);

        Organe::create([
            'membre_id' => $validated['membre_id'],
            'organe' => $validated['organe'],
            'poste' => $validated['poste'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'] ?? null,
            'actif' => true,
            'ordre' => $validated['ordre'] ?? 0,
        ]);

        return redirect()->back()
            ->with('success', 'Le membre a été ajouté à l\'organe avec succès.');
    }

    /**
     * Met à jour un poste dans un organe.
     */
    public function updateMembre(Request $request, Organe $organe)
    {
        $validated = $request->validate([
            'poste' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'actif' => ['nullable', 'boolean'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ], [
            'poste.required' => 'Le poste est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        ]);

        $organe->update([
            'poste' => $validated['poste'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'] ?? null,
            'actif' => $validated['actif'] ?? $organe->actif,
            'ordre' => $validated['ordre'] ?? $organe->ordre,
        ]);

        return redirect()->back()
            ->with('success', 'Le poste a été mis à jour avec succès.');
    }

    /**
     * Retire un membre d'un organe (désactive le mandat).
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

        // Gérer l'upload du PV
        if ($request->hasFile('pv_fichier')) {
            $validated['pv_fichier'] = $request->file('pv_fichier')->store('assemblees/pv', 'public');
        }

        // Calculer le quorum (majorité simple = 50% + 1)
        if (!empty($validated['membres_total']) && !empty($validated['membres_presents'])) {
            $validated['quorum_atteint'] = $validated['membres_presents'] >= ($validated['membres_total'] / 2);
        }

        AssembleeGenerale::create($validated);

        return redirect()->route('admin.gouvernance.assemblies')
            ->with('success', 'L\'Assemblée Générale a été enregistrée avec succès.');
    }

    /**
     * Met à jour une Assemblée Générale.
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

        // Gérer l'upload du PV
        if ($request->hasFile('pv_fichier')) {
            if ($assembly->pv_fichier) {
                Storage::disk('public')->delete($assembly->pv_fichier);
            }
            $validated['pv_fichier'] = $request->file('pv_fichier')->store('assemblees/pv', 'public');
        }

        // Calculer le quorum
        if (!empty($validated['membres_total']) && !empty($validated['membres_presents'])) {
            $validated['quorum_atteint'] = $validated['membres_presents'] >= ($validated['membres_total'] / 2);
        }

        $assembly->update($validated);

        return redirect()->route('admin.gouvernance.assemblies')
            ->with('success', 'L\'Assemblée Générale a été mise à jour avec succès.');
    }

    public function createAssembly()
{
    return view('admin.gouvernance.assembly-create');
}

public function editAssembly(AssembleeGenerale $assembly)
{
    return view('admin.gouvernance.assembly-edit', compact('assembly'));
}

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
 */
public function showAssembly(AssembleeGenerale $assembly)
{
    return view('admin.gouvernance.assembly-show', compact('assembly'));
}
}