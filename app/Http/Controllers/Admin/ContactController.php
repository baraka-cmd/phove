<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Nombre de conversations par page.
     */
    protected int $perPage = 20;

    /**
     * Statuts disponibles.
     */
    protected array $statuts = [
        'nouveau' => 'Nouveau',
        'en_cours' => 'En cours',
        'traite' => 'Traité',
        'archive' => 'Archivé',
    ];

    /**
     * Affiche la boîte de réception avec regroupement par expéditeur.
     */
    public function index(Request $request)
    {
        // Regrouper par email pour éviter les doublons d'affichage
        $query = Contact::selectRaw('
                email,
                MAX(nom) as nom,
                MAX(organisation) as organisation,
                MAX(sujet) as dernier_sujet,
                MAX(message) as dernier_message,
                MAX(id) as last_id,
                COUNT(*) as total_messages,
                SUM(CASE WHEN lu = 0 THEN 1 ELSE 0 END) as non_lus,
                MAX(statut) as dernier_statut,
                MAX(created_at) as derniere_date
            ')
            ->groupBy('email');

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->having('dernier_statut', $request->statut);
        }

        // Filtre par motif
        if ($request->filled('motif')) {
            $query->where('motif', $request->motif);
        }

        // Recherche textuelle
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('email', 'like', '%' . $recherche . '%')
                  ->orWhere('sujet', 'like', '%' . $recherche . '%')
                  ->orWhere('message', 'like', '%' . $recherche . '%')
                  ->orWhere('organisation', 'like', '%' . $recherche . '%');
            });
        }

        $conversations = $query->orderBy('derniere_date', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalMessages = Contact::count();
        $messagesNonLus = Contact::where('lu', false)->count();
        $messagesEnCours = Contact::where('statut', 'en_cours')->count();
        $messagesTraites = Contact::where('statut', 'traite')->count();
        $messagesArchives = Contact::where('statut', 'archive')->count();
        $messagesCeMois = Contact::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $traitesCeMois = Contact::where('statut', 'traite')
            ->whereMonth('date_reponse', now()->month)
            ->whereYear('date_reponse', now()->year)
            ->count();

        // Temps moyen de réponse (en heures)
        $tempsMoyenReponse = Contact::whereNotNull('date_reponse')
            ->whereNotNull('date_lecture')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, date_reponse)) as temps_moyen')
            ->value('temps_moyen');

        return view('admin.contacts.index', compact(
            'conversations',
            'totalMessages',
            'messagesNonLus',
            'messagesEnCours',
            'messagesTraites',
            'messagesArchives',
            'messagesCeMois',
            'traitesCeMois',
            'tempsMoyenReponse'
        ));
    }

    /**
     * Affiche une conversation complète (tous les messages d'un même expéditeur).
     */
    public function show($email)
    {
        $email = urldecode($email);

        $messages = Contact::where('email', $email)
            ->with('traitePar')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            return redirect()->route('admin.contacts.index')
                ->with('error', 'Aucun message trouvé pour cet expéditeur.');
        }

        // Marquer tous les messages de cette conversation comme lus
        $messagesNonLus = $messages->where('lu', false);
        foreach ($messagesNonLus as $message) {
            $message->marquerLu();
        }

        // Infos de l'expéditeur (premier message)
        $expediteur = $messages->first();
        $totalMessagesExp = $messages->count();

        // Conversations précédente et suivante
        $precedent = Contact::where('email', '<', $email)
            ->select('email')
            ->distinct()
            ->orderBy('email', 'desc')
            ->first();

        $suivant = Contact::where('email', '>', $email)
            ->select('email')
            ->distinct()
            ->orderBy('email', 'asc')
            ->first();

        // Admins pour assignation
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('admin.contacts.show', compact(
            'messages',
            'expediteur',
            'totalMessagesExp',
            'precedent',
            'suivant',
            'admins',
            'email'
        ));
    }

    /**
     * Marque un message comme lu.
     */
    public function marquerLu(Contact $contact)
    {
        $contact->marquerLu();

        return redirect()->back()
            ->with('success', 'Message marqué comme lu.');
    }

    /**
     * Marque un message comme non lu.
     */
    public function marquerNonLu(Contact $contact)
    {
        $contact->update([
            'lu' => false,
            'date_lecture' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Message marqué comme non lu.');
    }

    /**
     * Change le statut d'un message.
     */
    public function changeStatut(Request $request, Contact $contact)
    {
        $request->validate([
            'statut' => ['required', 'in:nouveau,en_cours,traite,archive'],
        ]);

        $contact->update(['statut' => $request->statut]);

        if ($request->statut === 'traite') {
            $contact->update([
                'repondu' => true,
                'date_reponse' => $contact->date_reponse ?? now(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Statut changé en "' . $this->statuts[$request->statut] . '".');
    }

    /**
     * Archive un message.
     */
    public function archiver(Contact $contact)
    {
        $contact->archiver();

        return redirect()->back()
            ->with('success', 'Message archivé.');
    }

    /**
     * Désarchive un message.
     */
    public function desarchiver(Contact $contact)
    {
        $contact->update(['statut' => 'en_cours']);

        return redirect()->back()
            ->with('success', 'Message désarchivé.');
    }

    /**
     * Enregistre une note interne sur un message.
     */
    public function note(Request $request, Contact $contact)
    {
        $request->validate([
            'note_interne' => ['nullable', 'string', 'max:2000'],
        ]);

        $contact->update([
            'note_interne' => $request->note_interne,
        ]);

        return redirect()->back()
            ->with('success', 'Note interne enregistrée.');
    }

    /**
     * Envoie une réponse par email au contact.
     */
    public function repondre(Request $request, Contact $contact)
    {
        $request->validate([
            'reponse' => ['required', 'string', 'max:5000'],
        ], [
            'reponse.required' => 'Le contenu de la réponse est obligatoire.',
        ]);

        // Envoyer l'email
        try {
            Mail::raw($request->reponse, function ($message) use ($contact, $request) {
                $message->to($contact->email, $contact->nom)
                    ->subject('Re: ' . $contact->sujet)
                    ->from(env('MAIL_FROM_ADDRESS', 'contact@ahd-rdc.org'), 'AHD-RDC');
            });

            // Marquer comme répondu
            $contact->update([
                'repondu' => true,
                'date_reponse' => now(),
                'statut' => 'traite',
                'traite_par' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('success', 'Réponse envoyée avec succès à ' . $contact->email . '.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }

    /**
     * Assigne un message à un administrateur.
     */
    public function assigner(Request $request, Contact $contact)
    {
        $request->validate([
            'traite_par' => ['nullable', 'exists:users,id'],
        ]);

        $contact->update([
            'traite_par' => $request->traite_par,
            'statut' => $contact->statut === 'nouveau' ? 'en_cours' : $contact->statut,
        ]);

        $admin = $request->traite_par ? User::find($request->traite_par) : null;
        $message = $admin ? 'Message assigné à ' . $admin->nom . '.' : 'Assignation retirée.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Supprime un message.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Le message a été supprimé définitivement.');
    }

    /**
     * Supprime toute une conversation (tous les messages d'un email).
     */
    public function destroyConversation(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $count = Contact::where('email', $request->email)->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', $count . ' message(s) supprimé(s) pour ' . $request->email . '.');
    }
}