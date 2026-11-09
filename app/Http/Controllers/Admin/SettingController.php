<?php

namespace App\\Http\\Controllers\\Admin;

use App\\Http\\Controllers\\Controller;
use App\\Models\\Setting;
use Illuminate\\Http\\JsonResponse;
use Illuminate\\Http\\RedirectResponse;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Validation\\Rule;
use Illuminate\\View\\View;
use Throwable;

class SettingController extends Controller
{
    protected array $groups = [
        'identite' => ['label' => 'Identité', 'icon' => 'fa-building', 'description' => 'Nom, logo, slogan et description de PHOVE.'],
        'contact' => ['label' => 'Contact', 'icon' => 'fa-address-book', 'description' => 'Adresse, téléphones, email et coordonnées du siège.'],
        'social' => ['label' => 'Réseaux sociaux', 'icon' => 'fa-share-alt', 'description' => 'Liens et coordonnées des réseaux sociaux officiels.'],
        'pages' => ['label' => 'Pages statiques', 'icon' => 'fa-file-alt', 'description' => 'Contenus éditoriaux des pages institutionnelles et de l’accueil.'],
        'seo' => ['label' => 'SEO', 'icon' => 'fa-search', 'description' => 'Métadonnées utilisées par les moteurs de recherche.'],
        'maintenance' => ['label' => 'Maintenance', 'icon' => 'fa-tools', 'description' => 'Mode maintenance et message destiné aux visiteurs.'],
    ];

    public function index(Request $request): View
    {
        $activeGroup = (string) $request->query('group', 'identite');

        if (!array_key_exists($activeGroup, $this->groups)) {
            $activeGroup = 'identite';
        }

        $settings = Setting::query()
            ->where('group', $activeGroup)
            ->orderBy('ordre')
            ->orderBy('label')
            ->get();

        $allSettings = Setting::query()->pluck('value', 'key')->all();

        return view('admin.settings.index', compact('settings', 'allSettings', 'activeGroup'));
    }

    public function update(Request $request): RedirectResponse
    {
        $activeGroup = (string) $request->input('group', 'identite');

        if (!array_key_exists($activeGroup, $this->groups)) {
            return redirect()->route('admin.parametres.index')
                ->with('error', 'Groupe de paramètres invalide.');
        }

        $settings = Setting::query()->where('group', $activeGroup)->get();

        if ($settings->isEmpty()) {
            return redirect()->route('admin.parametres.index', ['group' => $activeGroup])
                ->with('info', 'Aucun paramètre n’est encore configuré dans ce groupe.');
        }

        $rules = [];
        $messages = [];

        foreach ($settings as $setting) {
            $field = 'setting_' . $setting->key;

            switch ($setting->type) {
                case 'image':
                    $rules[$field] = ['sometimes', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
                    $messages[$field . '.image'] = 'Le fichier doit être une image JPG, PNG ou WebP.';
                    $messages[$field . '.mimes'] = 'Formats acceptés : JPG, PNG et WebP.';
                    $messages[$field . '.max'] = 'L’image ne doit pas dépasser 2 Mo.';
                    break;
                case 'email':
                    $rules[$field] = ['sometimes', 'nullable', 'string', 'email', 'max:255'];
                    break;
                case 'url':
                    $rules[$field] = ['sometimes', 'nullable', 'string', 'url', 'max:2048'];
                    break;
                case 'phone':
                    $rules[$field] = ['sometimes', 'nullable', 'string', 'max:30', 'regex:/^[+0-9\s\-().]+$/'];
                    $messages[$field . '.regex'] = 'Veuillez saisir un numéro de téléphone valide.';
                    break;
                case 'switch':
                    $rules[$field] = ['sometimes', 'boolean'];
                    break;
                case 'number':
                    $rules[$field] = ['sometimes', 'nullable', 'numeric'];
                    break;
                case 'color':
                    $rules[$field] = ['sometimes', 'nullable', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'];
                    $messages[$field . '.regex'] = 'La couleur doit être au format hexadécimal, par exemple #0F766E.';
                    break;
                case 'textarea':
                    $rules[$field] = ['sometimes', 'nullable', 'string', 'max:10000'];
                    break;
                case 'text':
                default:
                    $rules[$field] = ['sometimes', 'nullable', 'string', 'max:255'];
                    break;
            }

            if ($setting->type === 'image') {
                $rules['remove_' . $setting->key] = ['sometimes', 'boolean'];
            }
        }

        $request->validate($rules, $messages);

        // Étape 1 : stocker toutes les nouvelles images avant de toucher aux valeurs en base.
        // Ainsi, un échec de stockage ne détruit ni les anciennes images ni les autres réglages.
        $newUploads = [];

        try {
            foreach ($settings as $setting) {
                $field = 'setting_' . $setting->key;

                if ($setting->type !== 'image' || !$request->hasFile($field)) {
                    continue;
                }

                $newPath = media_store($request->file($field), 'settings');

                if (!$newPath) {
                    throw new \RuntimeException('Le stockage d’une nouvelle image a échoué.');
                }

                $newUploads[$setting->key] = $newPath;
            }
        } catch (Throwable $exception) {
            foreach ($newUploads as $uploadedPath) {
                try {
                    media_delete($uploadedPath);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            report($exception);

            return back()->withInput()
                ->with('error', 'Le stockage d’une nouvelle image a échoué. Aucune modification des paramètres n’a été enregistrée.');
        }

        $oldMediaToDelete = [];

        try {
            // Étape 2 : enregistrer les changements du groupe dans une transaction unique.
            $updatedCount = DB::transaction(function () use ($request, $settings, $newUploads, &$oldMediaToDelete): int {
                $count = 0;

                foreach ($settings as $setting) {
                    $field = 'setting_' . $setting->key;

                    if ($setting->type === 'image') {
                        if (isset($newUploads[$setting->key])) {
                            if ($setting->value && $setting->value !== $newUploads[$setting->key]) {
                                $oldMediaToDelete[] = $setting->value;
                            }

                            $setting->update(['value' => $newUploads[$setting->key]]);
                            $count++;
                            continue;
                        }

                        if ($request->boolean('remove_' . $setting->key) && $setting->value) {
                            $oldMediaToDelete[] = $setting->value;
                            $setting->update(['value' => null]);
                            $count++;
                        }

                        continue;
                    }

                    if ($setting->type === 'switch') {
                        $newValue = $request->boolean($field) ? '1' : '0';
                    } else {
                        // Seuls les champs présents dans le formulaire sont modifiables.
                        // Une valeur vide devient null et peut donc réellement être effacée.
                        if (!$request->has($field)) {
                            continue;
                        }

                        $newValue = $request->input($field);
                        if (is_string($newValue)) {
                            $newValue = trim($newValue);
                        }
                    }

                    $newValue = $newValue === null ? null : (string) $newValue;

                    if ($setting->value !== $newValue) {
                        $setting->update(['value' => $newValue]);
                        $count++;
                    }
                }

                return $count;
            });
        } catch (Throwable $exception) {
            // La transaction est annulée : supprimer les nouveaux fichiers orphelins.
            foreach ($newUploads as $uploadedPath) {
                try {
                    media_delete($uploadedPath);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        // Étape 3 : les nouvelles valeurs sont validées en base ; supprimer ensuite les anciens médias.
        foreach (array_unique($oldMediaToDelete) as $oldPath) {
            try {
                media_delete($oldPath);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        Setting::clearCache();

        $message = $updatedCount > 0
            ? $updatedCount . ' paramètre(s) mis à jour avec succès.'
            : 'Aucune modification détectée.';

        return redirect()->route('admin.parametres.index', ['group' => $activeGroup])
            ->with('success', $message);
    }

    public function removeImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', Rule::exists('settings', 'key')],
        ]);

        $setting = Setting::query()
            ->where('key', $validated['key'])
            ->where('type', 'image')
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Paramètre image introuvable.',
            ], 404);
        }

        if (!$setting->value) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune image n’est actuellement enregistrée.',
            ], 404);
        }

        $oldPath = $setting->value;
        $setting->update(['value' => null]);

        try {
            media_delete($oldPath);
        } catch (Throwable $exception) {
            report($exception);
        }

        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Image supprimée avec succès.',
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', Rule::exists('settings', 'key')],
        ]);

        $setting = Setting::query()->where('key', $validated['key'])->first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Paramètre introuvable.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $setting->key,
                'value' => $setting->value,
                'url' => $setting->type === 'image' ? media_url($setting->value) : null,
            ],
        ]);
    }
}
