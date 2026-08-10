<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Groupes de paramètres disponibles.
     */
    protected array $groups = [
        'identite' => [
            'label' => 'Identité',
            'icon' => 'fa-building',
            'description' => 'Nom, logo, slogan et description de l\'organisation.',
        ],
        'contact' => [
            'label' => 'Contact',
            'icon' => 'fa-address-book',
            'description' => 'Adresse, téléphone, email et autres coordonnées.',
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

        foreach ($existingSettings as $setting) {
            $inputName = 'setting_' . $setting->key;

            // Si c'est une image
            if ($setting->type === 'image') {
                if ($request->hasFile($inputName)) {
                    // Valider l'image
                    $request->validate([
                        $inputName => ['image', 'mimes:jpeg,jpg,png,webp,ico', 'max:2048'],
                    ], [
                        $inputName . '.image' => 'Le fichier doit être une image.',
                        $inputName . '.mimes' => 'L\'image doit être au format JPG, PNG, WebP ou ICO.',
                        $inputName . '.max' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ]);

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
                    // Validation selon le type
                    if ($setting->type === 'email' && $newValue) {
                        $request->validate([
                            $inputName => ['email'],
                        ], [
                            $inputName . '.email' => 'Veuillez saisir une adresse email valide.',
                        ]);
                    }
                    if ($setting->type === 'url' && $newValue) {
                        $request->validate([
                            $inputName => ['url'],
                        ], [
                            $inputName . '.url' => 'Veuillez saisir une URL valide (commençant par https://).',
                        ]);
                    }

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
     * Supprime le logo.
     */
    public function removeImage(Request $request)
    {
        $key = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->value) {
            Storage::disk('public')->delete($setting->value);
            $setting->update(['value' => null]);

            Setting::clearCache();

            return response()->json(['success' => true, 'message' => 'Image supprimée.']);
        }

        return response()->json(['success' => false, 'message' => 'Aucune image trouvée.'], 404);
    }

    /**
     * Affiche un paramètre spécifique (pour preview AJAX).
     */
    public function show(Request $request)
    {
        $key = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        if ($setting) {
            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'url' => $setting->type === 'image' ? $setting->urlValue() : null,
                ]
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}