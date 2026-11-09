<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Groupes de paramètres disponibles.
     *
     * @var array<string, array<string, string>>
     */
    protected array $groups = [
        'identite' => [
            'label'       => 'Identité',
            'icon'        => 'fa-building',
            'description' => 'Nom, logo, slogan et description de l\'organisation PHOVE.',
        ],
        'contact' => [
            'label'       => 'Contact',
            'icon'        => 'fa-address-book',
            'description' => 'Adresse, téléphones, email et autres coordonnées du siège de Goma.',
        ],
        'social' => [
            'label'       => 'Réseaux sociaux',
            'icon'        => 'fa-share-alt',
            'description' => 'Liens vers les comptes Facebook, Twitter, LinkedIn, YouTube, Instagram et WhatsApp.',
        ],
        'pages' => [
            'label'       => 'Pages statiques',
            'icon'        => 'fa-file-alt',
            'description' => 'Contenu des pages institutionnelles : Qui sommes-nous, Mission & Vision, Nos valeurs, Mentions légales.',
        ],
        'seo' => [
            'label'       => 'SEO',
            'icon'        => 'fa-search',
            'description' => 'Optimisation pour les moteurs de recherche : méta-titres, descriptions et mots-clés.',
        ],
        'maintenance' => [
            'label'       => 'Maintenance',
            'icon'        => 'fa-tools',
            'description' => 'Mode maintenance et message affiché aux visiteurs.',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le panneau de configuration avec tous les paramètres.
     */
    public function index(Request $request): View
    {
        // Groupe actif (par défaut : identite)
        $activeGroup = $request->input('group', 'identite');

        if (!array_key_exists($activeGroup, $this->groups)) {
            $activeGroup = 'identite';
        }

        // Récupérer les paramètres du groupe actif
        $settings = Setting::where('group', $activeGroup)
            ->orderBy('ordre')
            ->get();

        // Récupérer tous les paramètres pour la prévisualisation
        $allSettings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact(
            'settings',
            'allSettings',
            'activeGroup'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Met à jour tous les paramètres d'un groupe.
     */
    public function update(Request $request): RedirectResponse
    {
        $activeGroup = $request->input('group', 'identite');

        if (!array_key_exists($activeGroup, $this->groups)) {
            return redirect()->route('admin.parametres.index')
                ->with('error', 'Groupe de paramètres invalide.');
        }

        // Récupérer les paramètres existants du groupe
        $existingSettings = Setting::where('group', $activeGroup)->get();

        // Préparer les règles de validation dynamiques
        $validationRules    = [];
        $validationMessages = [];

        foreach ($existingSettings as $setting) {
            $inputName = 'setting_' . $setting->key;

            switch ($setting->type) {
                case 'image':
                    $validationRules[$inputName] = [
                        'nullable',
                        'image',
                        'mimes:jpeg,jpg,png,webp,ico,svg',
                        'max:2048',
                    ];
                    $validationMessages[$inputName . '.image'] = 'Le fichier doit être une image.';
                    $validationMessages[$inputName . '.mimes'] = 'Le fichier doit être au format JPG, PNG, WebP, ICO ou SVG.';
                    $validationMessages[$inputName . '.max']   = 'L\'image ne doit pas dépasser 2 Mo.';
                    break;

                case 'email':
                    $validationRules[$inputName] = ['nullable', 'email'];
                    $validationMessages[$inputName . '.email'] = 'Veuillez saisir une adresse email valide.';
                    break;

                case 'url':
                    $validationRules[$inputName] = ['nullable', 'url'];
                    $validationMessages[$inputName . '.url'] = 'Veuillez saisir une URL valide (commençant par https://).';
                    break;

                case 'phone':
                    $validationRules[$inputName] = [
                        'nullable',
                        'string',
                        'max:30',
                        'regex:/^[+0-9\s\-\(\)]+$/',
                    ];
                    $validationMessages[$inputName . '.regex'] = 'Veuillez saisir un numéro de téléphone valide.';
                    break;

                case 'number':
                    $validationRules[$inputName] = ['nullable', 'numeric'];
                    $validationMessages[$inputName . '.numeric'] = 'Veuillez saisir un nombre valide.';
                    break;
            }
        }

        // ✅ Validation AVANT tout traitement
        if (!empty($validationRules)) {
            $request->validate($validationRules, $validationMessages);
        }

        $updatedCount = 0;

        foreach ($existingSettings as $setting) {
            $inputName = 'setting_' . $setting->key;

            // ---- Image ----
            if ($setting->type === 'image') {
                // Suppression demandée
                $removeName = 'remove_' . $setting->key;
                if ($request->input($removeName) === '1') {
                    if ($setting->value) {
                        media_delete($setting->value);
                    }
                    $setting->update(['value' => null]);
                    $updatedCount++;
                    continue;
                }

                // Nouveau fichier envoyé
                if ($request->hasFile($inputName)) {
                    // Envoyer le nouveau fichier AVANT de toucher à l'ancien.
                    // Si Cloudinary ou le stockage échoue, l'image actuelle reste intacte.
                    $newPath = media_store($request->file($inputName), 'settings');

                    if ($newPath) {
                        $oldPath = $setting->value;

                        // Enregistrer d'abord le nouveau chemin en base.
                        $setting->update(['value' => $newPath]);
                        $updatedCount++;

                        // Supprimer l'ancien fichier uniquement après l'enregistrement réussi.
                        if ($oldPath && $oldPath !== $newPath) {
                            media_delete($oldPath);
                        }
                    }
                }
            }
            // ---- Switch (checkbox) ----
            elseif ($setting->type === 'switch') {
                $newValue = $request->has($inputName) ? '1' : '0';
                if ($setting->value !== $newValue) {
                    $setting->update(['value' => $newValue]);
                    $updatedCount++;
                }
            }
            // ---- Tous les autres types ----
            else {
                $newValue = $request->input($inputName);
                if ($newValue !== null && $newValue !== $setting->value) {
                    $setting->update(['value' => $newValue]);
                    $updatedCount++;
                }
            }
        }

        // Vider le cache (méthode statique du modèle Setting)
        if (method_exists(Setting::class, 'clearCache')) {
            Setting::clearCache();
        }

        $message = $updatedCount > 0
            ? $updatedCount . ' paramètre(s) mis à jour avec succès.'
            : 'Aucune modification détectée.';

        return redirect()->route('admin.parametres.index', ['group' => $activeGroup])
            ->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE IMAGE (AJAX)
    |--------------------------------------------------------------------------
    */

    /**
     * Supprime l'image d'un paramètre (AJAX).
     */
    public function removeImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => ['required', 'string', 'max:100', Rule::exists('settings', 'key')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Clé de paramètre invalide.',
            ], 422);
        }

        $key     = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        // Sécurité : le setting doit exister
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Paramètre introuvable.',
            ], 404);
        }

        // Vérifier que c'est bien une image
        if ($setting->type !== 'image') {
            return response()->json([
                'success' => false,
                'message' => 'Ce paramètre n\'est pas une image.',
            ], 400);
        }

        if (!$setting->value) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune image trouvée.',
            ], 404);
        }

        // Supprimer le fichier physique
        if ($setting->value) {
            media_delete($setting->value);
        }

        // Mettre à jour la base de données
        $setting->update(['value' => null]);

        // Vider le cache
        if (method_exists(Setting::class, 'clearCache')) {
            Setting::clearCache();
        }

        return response()->json([
            'success' => true,
            'message' => 'Image supprimée avec succès.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW (AJAX)
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche un paramètre spécifique (pour preview AJAX).
     */
    public function show(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => ['required', 'string', 'max:100', Rule::exists('settings', 'key')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Clé de paramètre invalide.',
            ], 422);
        }

        $key     = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Paramètre introuvable.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key'   => $setting->key,
                'value' => $setting->value,
                'url'   => $setting->type === 'image' && method_exists($setting, 'urlValue')
                    ? $setting->urlValue()
                    : null,
            ],
        ]);
    }
}