<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Nombre d'utilisateurs par page.
     */
    protected int $perPage = 15;

    /**
     * Rôles disponibles.
     */
    protected array $roles = [
        'super_admin' => 'Super Admin',
        'admin' => 'Administrateur',
        'editeur' => 'Éditeur',
    ];

    /**
     * Statuts disponibles.
     */
    protected array $statuts = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'suspendu' => 'Suspendu',
    ];

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
        $totalUsers = User::count();
        $usersActifs = User::where('statut', 'actif')->count();
        $usersInactifs = User::where('statut', 'inactif')->count();
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
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'mot_de_passe' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:super_admin,admin,editeur'],
            'statut' => ['required', 'in:actif,inactif,suspendu'],
            'photo_profil' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'photo_profil.image' => 'Le fichier doit être une image.',
            'photo_profil.mimes' => 'La photo doit être au format JPG, PNG ou WebP.',
            'photo_profil.max' => 'La photo ne doit pas dépasser 2 Mo.',
        ]);

        // Upload photo
        if ($request->hasFile('photo_profil')) {
            $validated['photo_profil'] = $request->file('photo_profil')->store('users/photos', 'public');
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
        $user->loadCount(['articles', 'donsEnregistres', 'cotisationsEnregistrees', 'medias', 'rapportsFinanciers', 'contactsTraites']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:super_admin,admin,editeur'],
            'statut' => ['required', 'in:actif,inactif,suspendu'],
            'photo_profil' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'role.required' => 'Le rôle est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
        ]);

        // Upload photo
        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne photo
            if ($user->photo_profil) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo_profil);
            }
            $validated['photo_profil'] = $request->file('photo_profil')->store('users/photos', 'public');
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

        // Supprimer la photo
        if ($user->photo_profil) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo_profil);
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
            'mot_de_passe.required' => 'Le nouveau mot de passe est obligatoire.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user->update([
            'mot_de_passe' => Hash::make($validated['mot_de_passe']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Le mot de passe de "' . $user->nom . '" a été modifié avec succès.');
    }
}