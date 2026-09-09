<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Groupes de paramètres disponibles.
     *
     * @var array<string, array<string, string>>
     */
    protected array $groups = [
        'identite' => [
            'label' => 'Identité',
            'icon' => 'fa-building',
            'description' => 'Nom, logo, slogan et description de l\'organisation PHOVE.',
        ],
        'contact' => [
            'label' => 'Contact',
            'icon' => 'fa-address-book',
            'description' => 'Adresse, téléphones, email et autres coordonnées du siège de Goma.',
        ],
        'social' => [
            'label' => 'Réseaux sociaux',
            'icon' => 'fa-share-alt',
            'description' => 'Liens vers les comptes Facebook, Twitter, LinkedIn, YouTube, Instagram et WhatsApp.',
        ],
        'pages' => [
            'label' => 'Pages statiques',
            'icon' => 'fa-file-alt',
            'description' => 'Contenu des pages institutionnelles : Qui sommes-nous, Mission & Vision, Nos valeurs, Mentions légales.',
        ],
        'seo' => [
            'label' => 'SEO',
            'icon' => 'fa-search',
            'description' => 'Optimisation pour les moteurs de recherche : méta-titres, descriptions et mots-clés.',
        ],
        'maintenance' => [
            'label' => 'Maintenance',
            'icon' => 'fa-tools',
            'description' => 'Mode maintenance et message affiché aux visiteurs.',
        ],
    ];

    /**
     * Affiche le panneau de configuration avec tous les paramètres.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
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

    /**
     * Met à jour tous les paramètres d'un groupe.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $activeGroup = $request->input('group', 'identite');

        if (!array_key_exists($activeGroup, $this->groups)) {
            return redirect()->route('admin.parametres.index')
                ->with('error', 'Groupe de paramètres invalide.');
        }

        // Récupérer les paramètres existants du groupe
        $existingSettings = Setting::where('group', $activeGroup)->get();

        $updatedCount = 0;
        $validationRules = [];
        $validationMessages = [];

        // Préparer les règles de validation dynamiques
        foreach ($existingSettings as $setting) {
            $inputName = 'setting_' . $setting->key;

            if ($setting->type === 'image') {
                if ($request->hasFile($inputName)) {
                    $validationRules[$inputName] = ['image', 'mimes:jpeg,jpg,png,webp,ico,svg', 'max:2048'];
                    $validationMessages[$inputName . '.image'] = 'Le fichier doit être une image.';
                    $validationMessages[$inputName . '.mimes'] = 'Le fichier doit être au format JPG, PNG, WebP, ICO ou SVG.';
                    $validationMessages[$inputName . '.max'] = 'L\'image ne doit pas dépasser 2 Mo.';
                }
            } elseif ($setting->type === 'email') {
                $validationRules[$inputName] = ['nullable', 'email'];
                $validationMessages[$inputName . '.email'] = 'Veuillez saisir une adresse email valide.';
            } elseif ($setting->type === 'url') {
                $validationRules[$inputName] = ['nullable', 'url'];
                $validationMessages[$inputName . '.url'] = 'Veuillez saisir une URL valide (commençant par https://).';
            } elseif ($setting->type === 'phone') {
                $validationRules[$inputName] = ['nullable', 'string', 'max:30', 'regex:/^[+0-9\s\-\(\)]+$/'];
                $validationMessages[$inputName . '.regex'] = 'Veuillez saisir un numéro de téléphone valide.';
            } elseif ($setting->type === 'number') {
                $validationRules[$inputName] = ['nullable', 'numeric'];
                $validationMessages[$inputName . '.numeric'] = 'Veuillez saisir un nombre valide.';
            }
        }

        // Valider les données avant tout traitement
        if (!empty($validationRules)) {
            $request->validate($validationRules, $validationMessages);
        }

        foreach ($existingSettings as $setting) {
            $inputName = 'setting_' . $setting->key;

            // Si c'est une image
            if ($setting->type === 'image') {
                if ($request->hasFile($inputName)) {
                    // Supprimer l'ancienne image
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Stocker la nouvelle
                    $path = $request->file($inputName)->store('settings', 'public');
                    $setting->update(['value' => $path]);
                    $updatedCount++;
                }

                // Si on a coché "supprimer l'image"
                $removeName = 'remove_' . $setting->key;
                if ($request->has($removeName) && $request->input($removeName) === '1') {
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $setting->update(['value' => null]);
                    $updatedCount++;
                }
            }
            // Si c'est un switch (checkbox)
            elseif ($setting->type === 'switch') {
                $newValue = $request->has($inputName) ? '1' : '0';
                if ($setting->value !== $newValue) {
                    $setting->update(['value' => $newValue]);
                    $updatedCount++;
                }
            }
            // Pour tous les autres types
            else {
                $newValue = $request->input($inputName);
                if ($newValue !== null && $newValue !== $setting->value) {
                    $setting->update(['value' => $newValue]);
                    $updatedCount++;
                }
            }
        }

        // Vider le cache
        Setting::clearCache();

        $message = $updatedCount > 0
            ? $updatedCount . ' paramètre(s) mis à jour avec succès.'
            : 'Aucune modification détectée.';

        return redirect()->route('admin.parametres.index', ['group' => $activeGroup])
            ->with('success', $message);
    }

    /**
     * Supprime l'image d'un paramètre (AJAX).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeImage(Request $request)
    {
        // Validation sécurisée
        $validator = Validator::make($request->all(), [
            'key' => ['required', 'string', 'max:100', Rule::exists('settings', 'key')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Clé de paramètre invalide.',
            ], 422);
        }

        $key = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        // Vérifier que c'est bien une image
        if ($setting->type !== 'image') {
            return response()->json([
                'success' => false,
                'message' => 'Ce paramètre n\'est pas une image.',
            ], 400);
        }

        if ($setting && $setting->value) {
            // Supprimer le fichier physique
            if (Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            // Mettre à jour la base de données
            $setting->update(['value' => null]);

            // Vider le cache
            Setting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Image supprimée avec succès.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune image trouvée.',
        ], 404);
    }

    /**
     * Affiche un paramètre spécifique (pour preview AJAX).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        // Validation sécurisée
        $validator = Validator::make($request->all(), [
            'key' => ['required', 'string', 'max:100', Rule::exists('settings', 'key')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Clé de paramètre invalide.',
            ], 422);
        }

        $key = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        if ($setting) {
            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'url' => $setting->type === 'image' ? $setting->urlValue() : null,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Paramètre introuvable.',
        ], 404);
    }
}