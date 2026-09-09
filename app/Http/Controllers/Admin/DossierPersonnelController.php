<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DossierPersonnel;
use App\Models\DocumentPersonnel;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DossierPersonnelController extends Controller
{
    /**
     * Nombre de dossiers par page.
     *
     * @var int
     */
    protected int $perPage = 15;

    /**
     * Affiche la liste paginée et filtrée des dossiers du personnel.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = DossierPersonnel::with('membre')
            ->withCount(['documents as documents_fournis_count' => function ($q) {
                $q->where('statut', 'fourni');
            }]);

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par membre
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->whereHas('membre', function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('post_nom', 'like', '%' . $recherche . '%')
                  ->orWhere('prenom', 'like', '%' . $recherche . '%');
            });
        }

        $dossiers = $query->orderBy('date_creation', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalDossiers = DossierPersonnel::count();
        $dossiersComplets = DossierPersonnel::where('statut', 'complet')->count();
        $dossiersIncomplets = DossierPersonnel::where('statut', 'incomplet')->count();
        $dossiersEnAttente = DossierPersonnel::where('statut', 'en_attente')->count();

        return view('admin.dossiers.index', compact(
            'dossiers',
            'totalDossiers',
            'dossiersComplets',
            'dossiersIncomplets',
            'dossiersEnAttente'
        ));
    }

    /**
     * Affiche la fiche détaillée d'un dossier.
     *
     * @param DossierPersonnel $dossier
     * @return \Illuminate\View\View
     */
    public function show(DossierPersonnel $dossier)
    {
        $dossier->load(['membre', 'documents', 'validePar']);

        $typesDocuments = DossierPersonnel::typesDocuments();
        $documentsParType = $dossier->documents->keyBy('type_document');

        return view('admin.dossiers.show', compact(
            'dossier',
            'typesDocuments',
            'documentsParType'
        ));
    }

    /**
     * Affiche le formulaire de création d'un dossier.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $membresSansDossier = Membre::where('statut', 'actif')
            ->whereDoesntHave('dossierPersonnel')
            ->orderBy('nom')
            ->orderBy('post_nom')
            ->get();

        $typesDocuments = DossierPersonnel::typesDocuments();

        return view('admin.dossiers.create', compact('membresSansDossier', 'typesDocuments'));
    }

    /**
     * Enregistre un nouveau dossier personnel avec upload des 12 pièces.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'membre_id' => [
                'required',
                'exists:membres,id',
                Rule::unique('dossiers_personnel', 'membre_id'),
            ],
            'date_creation' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'demande_motivation' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'cv' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'titres_scolaires' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'attestations_services' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'attestation_nationalite' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'attestations_autres' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'carte_nationalite' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'carte_hygiene' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'photos_passeport' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'carte_vocation' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'photos_famille' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
            'classeur' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
        ], [
            'membre_id.required' => 'Veuillez sélectionner un membre.',
            'membre_id.unique' => 'Ce membre a déjà un dossier personnel.',
            'date_creation.required' => 'La date de création est obligatoire.',
        ]);

        // Créer le dossier
        $dossier = DossierPersonnel::create([
            'membre_id' => $validated['membre_id'],
            'statut' => 'en_attente',
            'date_creation' => $validated['date_creation'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Créer les 12 documents et uploader les fichiers fournis
        $typesDocuments = DossierPersonnel::typesDocuments();
        $fichiersUploades = 0;

        foreach ($typesDocuments as $type => $label) {
            $statut = 'manquant';
            $fichier = null;
            $dateUpload = null;
            $uploadPar = null;

            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $nomFichier = $this->genererNomFichier($file, $type);
                $fichier = $file->storeAs(
                    'dossiers/' . $dossier->id . '/' . $type,
                    $nomFichier,
                    'public'
                );
                $statut = 'fourni';
                $dateUpload = now()->format('Y-m-d');
                $uploadPar = auth()->id();
                $fichiersUploades++;
            }

            DocumentPersonnel::create([
                'dossier_id' => $dossier->id,
                'type_document' => $type,
                'fichier' => $fichier,
                'date_upload' => $dateUpload,
                'upload_par' => $uploadPar,
                'statut' => $statut,
                'notes' => null,
            ]);
        }

        // Mettre à jour le statut du dossier
        $dossier->mettreAJourStatut();

        $message = 'Le dossier personnel a été créé avec succès.';
        if ($fichiersUploades > 0) {
            $message .= ' ' . $fichiersUploades . ' pièce(s) importée(s).';
        } else {
            $message .= ' Les 12 pièces restent à compléter.';
        }

        return redirect()->route('admin.dossiers.show', $dossier)
            ->with('success', $message);
    }

    /**
     * Upload un document pour une pièce spécifique.
     *
     * @param Request $request
     * @param DossierPersonnel $dossier
     * @param DocumentPersonnel $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadDocument(Request $request, DossierPersonnel $dossier, DocumentPersonnel $document)
    {
        $validated = $request->validate([
            'fichier' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp,pdf,doc,docx',
                'max:5120',
            ],
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier.',
            'fichier.mimes' => 'Le fichier doit être au format JPG, PNG, WebP, PDF, DOC ou DOCX.',
            'fichier.max' => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        // Vérifier que le document appartient bien au dossier
        if ($document->dossier_id !== $dossier->id) {
            abort(404);
        }

        // Supprimer l'ancien fichier s'il existe
        if ($document->fichier) {
            if (Storage::disk('public')->exists($document->fichier)) {
                Storage::disk('public')->delete($document->fichier);
            }
        }

        // Stocker le nouveau fichier avec le nom original
        $file = $request->file('fichier');
        $nomFichier = $this->genererNomFichier($file, $document->type_document);
        $chemin = $file->storeAs(
            'dossiers/' . $dossier->id . '/' . $document->type_document,
            $nomFichier,
            'public'
        );

        $document->update([
            'fichier' => $chemin,
            'date_upload' => now()->format('Y-m-d'),
            'upload_par' => auth()->id(),
            'statut' => 'fourni',
        ]);

        // Mettre à jour le statut du dossier
        $dossier->mettreAJourStatut();

        return redirect()->back()
            ->with('success', 'Le document « ' . $document->typeLibelle() . ' » a été uploadé avec succès.');
    }

    /**
     * Supprime un document uploadé (réinitialise la pièce à "manquant").
     *
     * @param DocumentPersonnel $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyDocument(DocumentPersonnel $document)
    {
        $dossier = $document->dossier;

        // Supprimer le fichier physique
        if ($document->fichier) {
            if (Storage::disk('public')->exists($document->fichier)) {
                Storage::disk('public')->delete($document->fichier);
            }
        }

        // Réinitialiser le document
        $document->update([
            'fichier' => null,
            'date_upload' => null,
            'upload_par' => null,
            'statut' => 'manquant',
        ]);

        // Mettre à jour le statut du dossier
        $dossier->mettreAJourStatut();

        return redirect()->back()
            ->with('success', 'Le document « ' . $document->typeLibelle() . ' » a été supprimé. La pièce est de nouveau marquée comme manquante.');
    }

    /**
     * Valide manuellement un dossier.
     *
     * @param DossierPersonnel $dossier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function valider(DossierPersonnel $dossier)
    {
        $fournis = $dossier->nombreDocumentsFournis();
        $total = count(DossierPersonnel::typesDocuments());

        if ($fournis < $total) {
            return redirect()->back()
                ->with('error', 'Impossible de valider : il manque ' . ($total - $fournis) . ' pièce(s).');
        }

        $dossier->update([
            'statut' => 'complet',
            'date_validation' => now()->format('Y-m-d'),
            'valide_par' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Le dossier a été validé comme complet avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'un dossier.
     *
     * @param DossierPersonnel $dossier
     * @return \Illuminate\View\View
     */
    public function edit(DossierPersonnel $dossier)
    {
        $dossier->load('documents');
        return view('admin.dossiers.edit', compact('dossier'));
    }

    /**
     * Met à jour un dossier personnel avec uploads des pièces.
     *
     * @param Request $request
     * @param DossierPersonnel $dossier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, DossierPersonnel $dossier)
    {
        $validated = $request->validate([
            'date_creation' => ['required', 'date'],
            'statut' => ['required', 'in:en_attente,incomplet,complet'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf,doc,docx', 'max:5120'],
        ], [
            'date_creation.required' => 'La date de création est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'documents.*.mimes' => 'Format non supporté pour une pièce.',
            'documents.*.max' => 'Un fichier ne doit pas dépasser 5 Mo.',
        ]);

        // Gérer les uploads des documents
        if ($request->has('documents')) {
            foreach ($request->file('documents') as $documentId => $file) {
                if ($file) {
                    $document = DocumentPersonnel::find($documentId);
                    if ($document && $document->dossier_id === $dossier->id) {
                        // Supprimer l'ancien fichier
                        if ($document->fichier) {
                            Storage::disk('public')->delete($document->fichier);
                        }
                        // Stocker le nouveau avec nom original
                        $nomFichier = $this->genererNomFichier($file, $document->type_document);
                        $chemin = $file->storeAs(
                            'dossiers/' . $dossier->id . '/' . $document->type_document,
                            $nomFichier,
                            'public'
                        );
                        $document->update([
                            'fichier' => $chemin,
                            'date_upload' => now()->format('Y-m-d'),
                            'upload_par' => auth()->id(),
                            'statut' => 'fourni',
                        ]);
                    }
                }
            }
        }

        // Mettre à jour le statut
        if ($validated['statut'] === 'complet' && $dossier->statut !== 'complet') {
            $validated['date_validation'] = now()->format('Y-m-d');
            $validated['valide_par'] = auth()->id();
        }
        if ($validated['statut'] !== 'complet') {
            $validated['date_validation'] = null;
            $validated['valide_par'] = null;
        }

        $dossier->update($validated);
        $dossier->mettreAJourStatut();

        return redirect()->route('admin.dossiers.show', $dossier)
            ->with('success', 'Le dossier a été mis à jour avec succès.');
    }

    /**
     * Supprime un dossier personnel.
     *
     * @param DossierPersonnel $dossier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(DossierPersonnel $dossier)
    {
        // Supprimer tous les fichiers physiques
        foreach ($dossier->documents as $document) {
            if ($document->fichier) {
                if (Storage::disk('public')->exists($document->fichier)) {
                    Storage::disk('public')->delete($document->fichier);
                }
            }
        }

        // Supprimer le dossier (cascade sur les documents)
        $dossier->delete();

        return redirect()->route('admin.dossiers.index')
            ->with('success', 'Le dossier personnel a été supprimé définitivement.');
    }

    /**
     * Affiche/ouvre un document personnel dans le navigateur.
     * Cette méthode contourne le problème de lien symbolique avec php artisan serve.
     *
     * @param DocumentPersonnel $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function voirDocument(DocumentPersonnel $document)
    {
        // Vérifier que le document a un fichier
        if (!$document->fichier) {
            abort(404, 'Aucun fichier pour ce document.');
        }

        // Construire le chemin complet du fichier
        $path = storage_path('app/public/' . $document->fichier);

        // Vérifier que le fichier existe physiquement
        if (!file_exists($path)) {
            abort(404, 'Fichier introuvable sur le serveur.');
        }

        // Retourner le fichier au navigateur
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Génère un nom de fichier sûr en conservant le nom original.
     * Ex: "Demande de motivation.pdf" → "demande-de-motivation-1699999999.pdf"
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $typeDocument
     * @return string
     */
    private function genererNomFichier($file, string $typeDocument): string
    {
        // Récupérer l'extension originale
        $extension = $file->getClientOriginalExtension();
        
        // Récupérer le nom original sans l'extension
        $nomOriginal = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Nettoyer le nom : remplacer espaces et caractères spéciaux par des tirets
        $nomNettoye = Str::slug($nomOriginal, '-');
        
        // Si le nom est vide après nettoyage, utiliser le type de document
        if (empty($nomNettoye)) {
            $nomNettoye = Str::slug($typeDocument, '-');
        }
        
        // Ajouter un timestamp pour éviter les doublons
        $timestamp = now()->timestamp;
        
        // Retourner le nom final
        return $nomNettoye . '-' . $timestamp . '.' . $extension;
    }
}