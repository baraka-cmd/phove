<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Nombre de documents par page.
     */
    protected int $perPage = 15;

    /**
     * Types de documents disponibles.
     */
    protected array $types = [
        'statuts' => 'Statuts',
        'roi' => 'Règlement d\'Ordre Intérieur',
        'proces_verbal' => 'Procès-verbal',
        'convention' => 'Convention de partenariat',
        'rapport_annuel' => 'Rapport annuel',
        'rapport_activite' => 'Rapport d\'activité',
        'guide_procedure' => 'Guide de procédure',
        'politique_interne' => 'Politique interne',
        'communique_officiel' => 'Communiqué officiel',
        'autre' => 'Autre document',
    ];

    /**
     * Extensions de fichiers acceptées.
     */
    protected array $extensionsAcceptees = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];

    /**
     * Taille maximale par fichier (20 Mo).
     */
    protected int $tailleMax = 20480;

    /**
     * Affiche la liste paginée et filtrée des documents.
     */
    public function index(Request $request)
    {
        $query = Document::with('uploadPar');

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par visibilité
        if ($request->filled('public')) {
            $query->where('public', $request->public === 'oui');
        }

        // Filtre par année
        if ($request->filled('annee')) {
            $query->whereYear('date_document', $request->annee);
        }

        // Recherche textuelle
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('signataires', 'like', '%' . $recherche . '%')
                  ->orWhere('version', 'like', '%' . $recherche . '%');
            });
        }

        $documents = $query->orderBy('date_document', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalDocuments = Document::count();
        $documentsPublics = Document::where('public', true)->count();
        $documentsPrives = Document::where('public', false)->count();
        $totalTelechargements = Document::sum('telechargements');

        // Années distinctes pour le filtre
        $annees = Document::selectRaw('YEAR(date_document) as annee')
            ->whereNotNull('date_document')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        return view('admin.documents.index', compact(
            'documents',
            'totalDocuments',
            'documentsPublics',
            'documentsPrives',
            'totalTelechargements',
            'annees'
        ));
    }

    /**
     * Affiche le formulaire d'upload d'un document.
     */
    public function create()
    {
        return view('admin.documents.create');
    }

    /**
     * Enregistre un nouveau document.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:' . implode(',', array_keys($this->types))],
            'fichier' => [
                'required',
                'file',
                'max:' . $this->tailleMax,
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, $this->extensionsAcceptees)) {
                        $fail('Le format "' . $extension . '" n\'est pas accepté. Formats acceptés : ' . implode(', ', $this->extensionsAcceptees));
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'date_document' => ['nullable', 'date'],
            'date_signature' => ['nullable', 'date'],
            'signataires' => ['nullable', 'string', 'max:500'],
            'version' => ['nullable', 'string', 'max:50'],
            'public' => ['nullable', 'boolean'],
            'categorie' => ['nullable', 'string', 'max:100'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'type.required' => 'Le type de document est obligatoire.',
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.max' => 'Le fichier ne doit pas dépasser ' . ($this->tailleMax / 1024) . ' Mo.',
        ]);

        // Upload du fichier
        $fichier = $request->file('fichier');
        $extension = strtolower($fichier->getClientOriginalExtension());
        $nomFichier = Str::uuid() . '.' . $extension;
        $chemin = $fichier->storeAs('documents', $nomFichier, 'public');

        Document::create([
            'titre' => $validated['titre'],
            'type' => $validated['type'],
            'fichier' => $chemin,
            'extension' => $extension,
            'taille' => $fichier->getSize(),
            'description' => $validated['description'] ?? null,
            'date_document' => $validated['date_document'] ?? now(),
            'date_signature' => $validated['date_signature'] ?? null,
            'signataires' => $validated['signataires'] ?? null,
            'version' => $validated['version'] ?? '1.0',
            'public' => $request->boolean('public'),
            'categorie' => $validated['categorie'] ?? null,
            'telechargements' => 0,
            'upload_par' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Le document "' . $validated['titre'] . '" a été importé avec succès.');
    }

    /**
     * Affiche le détail d'un document.
     */
    public function show(Document $document)
    {
        $document->load('uploadPar');

        // Documents précédent et suivant
        $precedent = Document::where('id', '<', $document->id)
            ->orderBy('id', 'desc')
            ->first();

        $suivant = Document::where('id', '>', $document->id)
            ->orderBy('id', 'asc')
            ->first();

        return view('admin.documents.show', compact('document', 'precedent', 'suivant'));
    }

    /**
     * Affiche le formulaire de modification d'un document.
     */
    public function edit(Document $document)
    {
        return view('admin.documents.edit', compact('document'));
    }

    /**
     * Met à jour les métadonnées d'un document.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:' . implode(',', array_keys($this->types))],
            'description' => ['nullable', 'string', 'max:2000'],
            'date_document' => ['nullable', 'date'],
            'date_signature' => ['nullable', 'date'],
            'signataires' => ['nullable', 'string', 'max:500'],
            'version' => ['nullable', 'string', 'max:50'],
            'public' => ['nullable', 'boolean'],
            'categorie' => ['nullable', 'string', 'max:100'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'type.required' => 'Le type de document est obligatoire.',
        ]);

        $validated['public'] = $request->boolean('public');

        $document->update($validated);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Le document a été mis à jour avec succès.');
    }

    /**
     * Supprime un document.
     */
    public function destroy(Document $document)
    {
        // Supprimer le fichier physique
        if ($document->fichier) {
            media_delete($document->fichier);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Le document a été supprimé définitivement.');
    }

    /**
     * Remplace le fichier d'un document.
     */
    public function replaceFile(Request $request, Document $document)
    {
        $request->validate([
            'fichier' => [
                'required',
                'file',
                'max:' . $this->tailleMax,
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, $this->extensionsAcceptees)) {
                        $fail('Format non accepté. Formats : ' . implode(', ', $this->extensionsAcceptees));
                    }
                },
            ],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
        ]);

        $fichier = $request->file('fichier');
        $extension = strtolower($fichier->getClientOriginalExtension());
        $nomFichier = Str::uuid() . '.' . $extension;
        $chemin = $fichier->storeAs('documents', $nomFichier, 'public');

        // Supprimer l'ancien fichier
        if ($document->fichier) {
            media_delete($document->fichier);
        }

        $document->update([
            'fichier' => $chemin,
            'extension' => $extension,
            'taille' => $fichier->getSize(),
        ]);

        return redirect()->back()
            ->with('success', 'Le fichier a été remplacé avec succès.');
    }

    /**
     * Bascule la visibilité publique/privée.
     */
    public function togglePublic(Document $document)
    {
        $document->update(['public' => !$document->public]);
        $etat = $document->public ? 'public' : 'privé';

        return redirect()->back()
            ->with('success', 'Le document est maintenant ' . $etat . '.');
    }

    /**
 * Télécharge un document et incrémente le compteur.
 */
public function download(Document $document)
{
    if (!$document->fichier) {
        return redirect()->back()
            ->with('error', 'Le fichier est introuvable.');
    }

    // Incrémenter le compteur
    $document->increment('telechargements');

    $nomFichier = Str::slug($document->titre) . '.' . $document->extension;

    // URL Cloudinary → redirection
    if (str_starts_with($document->fichier, 'http')) {
        return redirect($document->fichier);
    }

    // Fichier local → téléchargement classique
    return Storage::disk('public')->download($document->fichier, $nomFichier);
}

    /**
 * Affiche le fichier dans le navigateur (pour PDF).
 */
public function preview(Document $document)
{
    if (!$document->fichier) {
        return redirect()->back()
            ->with('error', 'Le fichier est introuvable.');
    }

    // URL Cloudinary → redirection
    if (str_starts_with($document->fichier, 'http')) {
        return redirect($document->fichier);
    }

    // Fichier local → aperçu direct
    return response()->file(Storage::disk('public')->path($document->fichier));
}
}