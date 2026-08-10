<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Nombre de conversations par page.
     */
    protected int $perPage = 20;

    /**
     * Affiche la boîte de réception (conversations groupées par interlocuteur).
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Conversations reçues : regroupées par expéditeur
        $conversationsRecues = Message::selectRaw('
                expediteur_id as interlocuteur_id,
                MAX(created_at) as derniere_date,
                COUNT(*) as total_messages,
                SUM(CASE WHEN lu = 0 THEN 1 ELSE 0 END) as non_lus,
                MAX(CASE WHEN lu = 0 THEN 1 ELSE 0 END) as has_unread,
                "recu" as type
            ')
            ->where('destinataire_id', $userId)
            ->where('archived_dest', false)
            ->groupBy('expediteur_id')
            ->get();

        // Conversations envoyées : regroupées par destinataire (seulement si pas déjà dans reçues)
        $conversationsEnvoyees = Message::selectRaw('
                destinataire_id as interlocuteur_id,
                MAX(created_at) as derniere_date,
                COUNT(*) as total_messages,
                0 as non_lus,
                0 as has_unread,
                "envoye" as type
            ')
            ->where('expediteur_id', $userId)
            ->where('archived_exp', false)
            ->whereNotIn('destinataire_id', $conversationsRecues->pluck('interlocuteur_id'))
            ->groupBy('destinataire_id')
            ->get();

        // Fusionner les deux listes
        $allConversations = $conversationsRecues->concat($conversationsEnvoyees)
            ->sortByDesc('derniere_date')
            ->values();

        // Paginer manuellement
        $page = $request->input('page', 1);
        $total = $allConversations->count();
        $conversations = new \Illuminate\Pagination\LengthAwarePaginator(
            $allConversations->forPage($page, $this->perPage),
            $total,
            $this->perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Charger les utilisateurs
        $interlocuteurIds = $allConversations->pluck('interlocuteur_id')->unique();
        $interlocuteurs = User::whereIn('id', $interlocuteurIds)->get()->keyBy('id');
        $conversations->getCollection()->transform(function ($conv) use ($interlocuteurs) {
            $conv->interlocuteur = $interlocuteurs->get($conv->interlocuteur_id);
            return $conv;
        });

        // Statistiques
        $totalMessages = Message::where('destinataire_id', $userId)
            ->where('archived_dest', false)
            ->count();
        $messagesNonLus = Message::where('destinataire_id', $userId)
            ->where('lu', false)
            ->where('archived_dest', false)
            ->count();
        $messagesEnvoyes = Message::where('expediteur_id', $userId)
            ->where('archived_exp', false)
            ->count();

        // Utilisateurs disponibles pour nouveau message
        $utilisateurs = User::where('id', '!=', $userId)
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('admin.messages.index', compact(
            'conversations',
            'totalMessages',
            'messagesNonLus',
            'messagesEnvoyes',
            'utilisateurs'
        ));
    }

    /**
     * Affiche les messages envoyés uniquement.
     */
    public function envoyes(Request $request)
    {
        $userId = Auth::id();

        $messages = Message::with(['destinataire'])
            ->where('expediteur_id', $userId)
            ->where('archived_exp', false)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('admin.messages.envoyes', compact('messages'));
    }

    /**
     * Affiche la conversation avec un utilisateur spécifique.
     */
    public function show(User $user)
    {
        $userId = Auth::id();

        // Récupérer tous les messages entre les deux utilisateurs
        $messages = Message::where(function ($query) use ($userId, $user) {
                $query->where('expediteur_id', $userId)
                      ->where('destinataire_id', $user->id)
                      ->where('archived_exp', false);
            })
            ->orWhere(function ($query) use ($userId, $user) {
                $query->where('expediteur_id', $user->id)
                      ->where('destinataire_id', $userId)
                      ->where('archived_dest', false);
            })
            ->with(['expediteur', 'destinataire'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer tous les messages reçus comme lus
        $messagesNonLus = $messages->where('destinataire_id', $userId)->where('lu', false);
        foreach ($messagesNonLus as $message) {
            $message->marquerLu($userId);
        }

        $totalMessages = $messages->count();

        return view('admin.messages.show', compact('messages', 'user', 'totalMessages'));
    }

    /**
     * Affiche le formulaire pour composer un nouveau message.
     */
    public function composer(Request $request)
    {
        $userId = Auth::id();

        $utilisateurs = User::where('id', '!=', $userId)
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        $destinataire = null;
        if ($request->filled('destinataire_id')) {
            $destinataire = User::find($request->destinataire_id);
        }

        return view('admin.messages.composer', compact('utilisateurs', 'destinataire'));
    }

    /**
     * Envoie un nouveau message.
     */
    public function envoyer(Request $request)
    {
        $validated = $request->validate([
            'destinataire_id' => ['required', 'exists:users,id'],
            'sujet' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string', 'max:5000'],
        ], [
            'destinataire_id.required' => 'Veuillez sélectionner un destinataire.',
            'destinataire_id.exists' => 'Le destinataire sélectionné n\'existe pas.',
            'sujet.required' => 'Le sujet est obligatoire.',
            'contenu.required' => 'Le contenu du message est obligatoire.',
        ]);

        // Vérifier qu'on ne s'envoie pas un message à soi-même
        if ($validated['destinataire_id'] == Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas vous envoyer un message à vous-même.')
                ->withInput();
        }

        Message::create([
            'expediteur_id' => Auth::id(),
            'destinataire_id' => $validated['destinataire_id'],
            'sujet' => $validated['sujet'],
            'contenu' => $validated['contenu'],
            'lu' => false,
        ]);

        $destinataire = User::find($validated['destinataire_id']);

        return redirect()->route('admin.messages.show', $destinataire)
            ->with('success', 'Message envoyé avec succès à ' . $destinataire->nom . '.');
    }

    /**
     * Répond à un message dans une conversation.
     */
    public function repondre(Request $request, User $user)
    {
        $validated = $request->validate([
            'contenu' => ['required', 'string', 'max:5000'],
        ], [
            'contenu.required' => 'Le contenu de la réponse est obligatoire.',
        ]);

        // Récupérer le dernier sujet de la conversation
        $dernierMessage = Message::where(function ($query) use ($user) {
                $query->where('expediteur_id', Auth::id())
                      ->where('destinataire_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('expediteur_id', $user->id)
                      ->where('destinataire_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->first();

        $sujet = $dernierMessage ? 'Re: ' . $dernierMessage->sujet : 'Nouveau message';

        Message::create([
            'expediteur_id' => Auth::id(),
            'destinataire_id' => $user->id,
            'sujet' => $sujet,
            'contenu' => $validated['contenu'],
            'lu' => false,
        ]);

        return redirect()->route('admin.messages.show', $user)
            ->with('success', 'Réponse envoyée avec succès.');
    }

    /**
     * Marque un message spécifique comme lu.
     */
    public function marquerLu(Message $message)
    {
        if ($message->destinataire_id === Auth::id()) {
            $message->marquerLu(Auth::id());
        }

        return redirect()->back()
            ->with('success', 'Message marqué comme lu.');
    }

    /**
     * Archive une conversation entière avec un utilisateur.
     */
    public function archiverConversation(User $user)
    {
        $userId = Auth::id();

        // Archiver les messages reçus
        Message::where('destinataire_id', $userId)
            ->where('expediteur_id', $user->id)
            ->update(['archived_dest' => true]);

        // Archiver les messages envoyés
        Message::where('expediteur_id', $userId)
            ->where('destinataire_id', $user->id)
            ->update(['archived_exp' => true]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Conversation avec ' . $user->nom . ' archivée.');
    }

    /**
     * Supprime un message spécifique.
     */
    public function destroy(Message $message)
    {
        // Vérifier que l'utilisateur est bien l'expéditeur ou le destinataire
        if ($message->expediteur_id !== Auth::id() && $message->destinataire_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->back()
            ->with('success', 'Message supprimé.');
    }
}