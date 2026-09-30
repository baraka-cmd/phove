<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Nombre d'utilisateurs par page.
     */
    protected int $perPage = 15;

    /**
     * Statuts disponibles.
     */
    protected array $statuts = [
        'actif'    => 'Actif',
        'inactif'  => 'Inactif',
        'suspendu' => 'Suspendu',
    ];

    /**
     * Retourne la liste des rôles autorisés (source unique : l'Enum Roles).
     *
     * @return array<string, string>
     */
    protected function rolesDisponibles(): array
    {
        return Roles::adminRoles();
    }

    /**
     * Retourne la liste des clés de rôles autorisés (pour Rule::in).
     *
     * @return array<int, string>
     */
    protected function rolesValides(): array
    {
        return array_keys($this->rolesDisponibles());
    }

    /**
     * Retourne la liste des clés de statuts autorisés.
     *
     * @return array<int, string>
     */
    protected function statutsValides(): array
    {
        return array_keys($this->statuts);
    }

    /**
     * Affiche la liste paginée et filtrée des utilisateurs.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
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
                  ->orWhere('email', 'like', '%' . $recherche . '%')
                  ->orWhere('telephone', 'like', '%' . $recherche . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalUsers     = User::count();
        $usersActifs    = User::where('statut', 'actif')->count();
        $usersInactifs  = User::where('statut', 'inactif')->count();
        $usersSuspendus = User::where('statut', 'suspendu')->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'usersActifs',
            'usersInactifs',
            'usersSuspendus'
        ));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur.
     */
    public function create()
    {
        $roles   = $this->rolesDisponibles();
        $statuts = $this->statuts;

        return view('admin.users.create', compact('roles', 'statuts'));
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'          => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:191', 'unique:users,email'],
            'telephone'    => ['nullable', 'string', 'max:20'],
            'mot_de_passe' => ['required', 'string', 'min:8', 'confirmed'],
            'role'         => ['required', Rule::in($this->rolesValides())],
            'statut'       => ['required', Rule::in($this->statutsValides())],
            'photo_profil' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ], [
            'nom.required'                 => 'Le nom est obligatoire.',
            'email.required'               => 'L\'adresse email est obligatoire.',
            'email.email'                  => 'Veuillez saisir une adresse email valide.',
            'email.unique'                 => 'Cette adresse email est déjà utilisée.',
            'mot_de_passe.required'        => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min'             => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed'       => 'La confirmation du mot de passe ne correspond pas.',
            'role.required'                => 'Le rôle est obligatoire.',
            'role.in'                      => 'Le rôle sélectionné n\'est pas valide.',
            'statut.required'              => 'Le statut est obligatoire.',
            'statut.in'                    => 'Le statut sélectionné n\'est pas valide.',
            'photo_profil.image'           => 'Le fichier doit être une image.',
            'photo_profil.mimes'           => 'La photo doit être au format JPG, PNG ou WebP.',
            'photo_profil.max'             => 'La photo ne doit pas dépasser 2 Mo.',
        ]);

        // Upload photo
        if ($request->hasFile('photo_profil')) {
            $validated['photo_profil'] = media_store($request->file('photo_profil'), 'users/photos');
        }

        // Hasher le mot de passe
        $validated['mot_de_passe'] = Hash::make($validated['mot_de_passe']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'L\'utilisateur "' . $validated['nom'] . '" a été créé avec succès.');
    }

    /**
     * Affiche le détail d'un utilisateur.
     */
    public function show(User $user)
    {
        $user->loadCount([
            'articles',
            'donsEnregistres',
            'cotisationsEnregistrees',
            'medias',
            'rapportsFinanciers',
            'contactsTraites',
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur.
     */
    public function edit(User $user)
    {
        $roles   = $this->rolesDisponibles();
        $statuts = $this->statuts;

        return view('admin.users.edit', compact('user', 'roles', 'statuts'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        // Protection : on ne modifie pas son propre rôle / statut
        if ($user->id === Auth::id()) {
            $request->merge([
                'role'   => $user->role,
                'statut' => $user->statut,
            ]);
        }

        $validated = $request->validate([
            'nom'          => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'telephone'    => ['nullable', 'string', 'max:20'],
            'role'         => ['required', Rule::in($this->rolesValides())],
            'statut'       => ['required', Rule::in($this->statutsValides())],
            'photo_profil' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ], [
            'nom.required'    => 'Le nom est obligatoire.',
            'email.required'  => 'L\'adresse email est obligatoire.',
            'email.email'     => 'Veuillez saisir une adresse email valide.',
            'email.unique'    => 'Cette adresse email est déjà utilisée.',
            'role.required'   => 'Le rôle est obligatoire.',
            'role.in'         => 'Le rôle sélectionné n\'est pas valide.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in'       => 'Le statut sélectionné n\'est pas valide.',
        ]);

        // Upload photo
        if ($request->hasFile('photo_profil')) {
            if ($user->photo_profil) {
                media_delete($user->photo_profil);
            }
            $validated['photo_profil'] = media_store($request->file('photo_profil'), 'users/photos');
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'L\'utilisateur "' . $user->nom . '" a été mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        // Protection : ne pas se supprimer soi-même
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Protection : ne pas supprimer le dernier Super Admin
        if ($user->role === Roles::SUPER_ADMIN
            && User::where('role', Roles::SUPER_ADMIN)->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer le dernier Super Admin.');
        }

        // Supprimer la photo
        if ($user->photo_profil) {
            media_delete($user->photo_profil);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'L\'utilisateur a été supprimé définitivement.');
    }

    /**
     * Active un utilisateur.
     */
    public function activer(User $user)
    {
        $user->update(['statut' => 'actif']);

        return redirect()->back()
            ->with('success', 'Le compte de "' . $user->nom . '" a été activé.');
    }

    /**
     * Désactive un utilisateur.
     */
    public function desactiver(User $user)
    {
        // Protection : ne pas se désactiver soi-même
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['statut' => 'inactif']);

        return redirect()->back()
            ->with('success', 'Le compte de "' . $user->nom . '" a été désactivé.');
    }

    /**
     * Suspend un utilisateur.
     */
    public function suspendre(User $user)
    {
        // Protection : ne pas se suspendre soi-même
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas suspendre votre propre compte.');
        }

        // Protection : ne pas suspendre le dernier Super Admin
        if ($user->role === Roles::SUPER_ADMIN
            && User::where('role', Roles::SUPER_ADMIN)->count() <= 1) {
            return redirect()->back()
                ->with('error', 'Impossible de suspendre le dernier Super Admin.');
        }

        $user->update(['statut' => 'suspendu']);

        return redirect()->back()
            ->with('success', 'Le compte de "' . $user->nom . '" a été suspendu.');
    }

    /**
     * Affiche le formulaire de changement de mot de passe.
     */
    public function editPassword(User $user)
    {
        return view('admin.users.password', compact('user'));
    }

    /**
     * Change le mot de passe d'un utilisateur.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'mot_de_passe' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'mot_de_passe.required'  => 'Le nouveau mot de passe est obligatoire.',
            'mot_de_passe.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user->update([
            'mot_de_passe' => Hash::make($validated['mot_de_passe']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Le mot de passe de "' . $user->nom . '" a été modifié avec succès.');
    }
}