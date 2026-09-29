<?php

namespace App\Services\Stock;

use App\Enums\StatutReservation;
use App\Models\LigneReservationStock;
use App\Models\ReservationStock;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Service métier des réservations de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une réservation BLOQUE une quantité pour un usage futur,
 *   SANS effectuer de sortie physique.
 *
 *   Tant qu'elle est active (confirmee | partielle), sa
 *   quantité non encore consommée est retirée de la
 *   disponibilité de l'article.
 *
 * CYCLE DE VIE :
 *
 *   brouillon
 *       │ (confirmer)
 *       ▼
 *   confirmee ───┐
 *       │        │ (annuler)
 *       │ (consommation partielle)
 *       ▼        │
 *   partielle ───┤
 *       │        │
 *       │ (consommation totale)
 *       ▼        ▼
 *   consommee  annulee
 *       ▲
 *       │ (délai dépassé)
 *       │
 *   expiree
 *
 * INVARIANTS :
 *
 *   - Seules CONFIRMEE et PARTIELLE impactent la disponibilité.
 *   - Une réservation BROUILLON ne bloque RIEN.
 *   - Une réservation terminée (consommee | expiree | annulee)
 *     ne peut plus changer d'état.
 *   - La quantité réservée active d'une ligne est :
 *       quantite_reservee - quantite_consommee
 *   - Aucune opération ne modifie directement les lots ni
 *     les mouvements : la réservation est un BLOCAGE logique.
 *
 * CONCURRENCE :
 *
 *   Toutes les opérations critiques (confirmer, consommer,
 *   annuler, expirer) sont transactionnelles et verrouillent :
 *     - la réservation,
 *     - les stocks concernés (triés par id),
 *     - éventuellement les lignes.
 */
class ReservationStockService
{
    /*
    |--------------------------------------------------------------------------
    | REQUÊTE (LECTURE)
    |--------------------------------------------------------------------------
    */

    /**
     * Prépare la requête de recherche des réservations.
     */
    public function requete(
        ?string $recherche = null,
        ?string $statut = null,
        ?int $projetId = null,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): Builder {
        return ReservationStock::query()
            ->when(
                $recherche,
                fn (Builder $q, string $r) => $q->recherche($r)
            )
            ->when(
                $statut,
                fn (Builder $q, string $s) => $q->where('statut', $s)
            )
            ->when(
                $projetId && $projetId > 0,
                fn (Builder $q) => $q->where('projet_id', $projetId)
            )
            ->when(
                $dateDebut && trim($dateDebut) !== '',
                fn (Builder $q) => $q->whereDate('date_reservation', '>=', $dateDebut)
            )
            ->when(
                $dateFin && trim($dateFin) !== '',
                fn (Builder $q) => $q->whereDate('date_reservation', '<=', $dateFin)
            );
    }

    /**
     * Récupère une réservation par identifiant, avec relations.
     */
    public function trouver(int $id): ReservationStock
    {
        return ReservationStock::query()
            ->with([
                'lignes.stock',
                'demandeur',
                'projet',
            ])
            ->findOrFail($id);
    }

        /*
    |--------------------------------------------------------------------------
    | RÉSUMÉ
    |--------------------------------------------------------------------------
    |
    | Retourne un résumé chiffré de la réservation :
    |   - nombre de lignes
    |   - quantité demandée
    |   - quantité réservée
    |   - quantité consommée
    |   - reste réservé
    |
    | Utilisé par ReservationStockController::show()
    | pour alimenter la vue de détail.
    */

    /**
     * Retourne un résumé chiffré d'une réservation.
     *
     * @param ReservationStock $reservation
     * @return array<string, mixed>
     */
    public function resume(ReservationStock $reservation): array
    {
        $reservation->loadMissing('lignes');

        $lignes = $reservation->lignes;

        $totalDemande = round(
            (float) $lignes->sum('quantite_demandee'),
            3
        );

        $totalReserve = round(
            (float) $lignes->sum('quantite_reservee'),
            3
        );

        $totalConsomme = round(
            (float) $lignes->sum('quantite_consommee'),
            3
        );

        $reste = max(
            0,
            round($totalReserve - $totalConsomme, 3)
        );

        return [
            'nombre_lignes'      => $lignes->count(),
            'quantite_demandee'  => $totalDemande,
            'quantite_reservee'  => $totalReserve,
            'quantite_consommee' => $totalConsomme,
            'reste_reserve'      => $reste,
        ];
    }
    
    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Crée une réservation avec ses lignes.
     *
     * AUCUN blocage réel à ce stade (statut BROUILLON).
     *
     * @throws Throwable
     */
    public function creer(
        array $data,
        int $utilisateurId
    ): ReservationStock {
        return DB::transaction(function () use ($data, $utilisateurId) {

            $lignes = $data['lignes'] ?? [];

            if (empty($lignes)) {
                throw ValidationException::withMessages([
                    'lignes' =>
                        'Une réservation doit contenir au moins un article.',
                ]);
            }

            $this->validerLignes($lignes);

            $reservation = ReservationStock::create([
                'numero'           => $this->genererNumero(),
                'date_reservation' => $data['date_reservation']
                    ?? now()->toDateString(),
                'date_expiration'  => $data['date_expiration'] ?? null,
                'demandeur_id'     => $data['demandeur_id'] ?? $utilisateurId,
                'projet_id'        => $data['projet_id'] ?? null,
                'destinataire'     => $data['destinataire'] ?? null,
                'motif'            => $data['motif'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'statut'           => StatutReservation::BROUILLON->value,
            ]);

            foreach ($lignes as $ligne) {
                $this->creerLigne($reservation, $ligne);
            }

            return $reservation->fresh([
                'lignes.stock',
                'demandeur',
                'projet',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION
    |--------------------------------------------------------------------------
    */

    /**
     * Confirme une réservation : bloque réellement les quantités.
     *
     * Vérifie la disponibilité de chaque article sous verrouillage.
     *
     * @throws Throwable
     */
    public function confirmer(ReservationStock $reservation): ReservationStock
    {
        return DB::transaction(function () use ($reservation) {

            /*
             * Lock de la réservation.
             */
            $reservation = ReservationStock::query()
                ->lockForUpdate()
                ->find($reservation->id);

            if (!$reservation) {
                throw (new ModelNotFoundException())
                    ->setModel(ReservationStock::class, [$reservation->id]);
            }

            if (!$reservation->peutEtreConfirmee()) {
                throw ValidationException::withMessages([
                    'reservation' => sprintf(
                        'Une réservation "%s" ne peut pas être confirmée.',
                        $reservation->statut->libelle()
                    ),
                ]);
            }

            /*
             * Chargement des lignes.
             */
            $reservation->load('lignes');

            if ($reservation->lignes->isEmpty()) {
                throw ValidationException::withMessages([
                    'lignes' =>
                        'Impossible de confirmer une réservation sans ligne.',
                ]);
            }

            /*
             * Lock des stocks concernés (triés pour éviter les deadlocks).
             */
            $stockIds = $reservation->lignes
                ->pluck('stock_id')
                ->unique()
                ->sort()
                ->values();

            $stocks = Stock::query()
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            /*
             * Vérification de la disponibilité pour chaque article.
             */
            foreach ($reservation->lignes as $ligne) {

                $stock = $stocks->get($ligne->stock_id);

                if (!$stock) {
                    throw ValidationException::withMessages([
                        'stock' => sprintf(
                            'Le stock #%d n’existe plus.',
                            $ligne->stock_id
                        ),
                    ]);
                }

                $quantiteDemandee = $this->decimal($ligne->quantite_demandee);

                if ($quantiteDemandee <= 0) {
                    throw ValidationException::withMessages([
                        'lignes' =>
                            'Toutes les quantités doivent être strictement positives.',
                    ]);
                }

                $disponible = $stock->quantiteDisponible();

                if ($quantiteDemandee > $disponible) {
                    throw ValidationException::withMessages([
                        'stock' => sprintf(
                            'Stock insuffisant pour "%s". Disponible : %s, demandé : %s.',
                            $stock->designation,
                            $disponible,
                            $quantiteDemandee
                        ),
                    ]);
                }

                /*
                 * Fixation de la quantité réellement réservée.
                 *
                 * À ce stade = quantité demandée. Une réservation
                 * partielle (quantité réservée < demandée) n'est
                 * PAS supportée par cette version : toute demande
                 * est acceptée en totalité ou refusée.
                 */
                $ligne->quantite_reservee = $quantiteDemandee;
                $ligne->save();
            }

            /*
             * MAJ du statut.
             */
            $reservation->update([
                'statut' => StatutReservation::CONFIRMEE->value,
            ]);

            return $reservation->fresh([
                'lignes.stock',
                'demandeur',
                'projet',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONSOMMATION
    |--------------------------------------------------------------------------
    */

    /**
     * Consomme une réservation (partiellement ou totalement).
     *
     * @param array<int, float>|null $consommations
     *     Tableau [ligne_id => quantite]. Si null, toute la
     *     réservation est consommée.
     *
     * @throws Throwable
     */
    public function consommer(
        ReservationStock $reservation,
        ?array $consommations = null
    ): ReservationStock {
        return DB::transaction(function () use (
            $reservation,
            $consommations
        ) {

            $reservation = ReservationStock::query()
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if (!$reservation->peutEtreConsommee()) {
                throw ValidationException::withMessages([
                    'reservation' => sprintf(
                        'Une réservation "%s" ne peut pas être consommée.',
                        $reservation->statut->libelle()
                    ),
                ]);
            }

            $reservation->load('lignes');

            /*
             * Consommation par défaut : tout.
             */
            if ($consommations === null) {
                $consommations = $reservation->lignes
                    ->mapWithKeys(fn (LigneReservationStock $l) => [
                        $l->id => (float) $l->quantite_reservee
                            - (float) $l->quantite_consommee,
                    ])
                    ->all();
            }

            foreach ($consommations as $ligneId => $quantite) {

                $ligne = $reservation->lignes->firstWhere('id', $ligneId);

                if (!$ligne) {
                    throw ValidationException::withMessages([
                        'lignes' => sprintf(
                            'La ligne #%d n’appartient pas à cette réservation.',
                            $ligneId
                        ),
                    ]);
                }

                $quantite = $this->decimal($quantite);

                if ($quantite <= 0) {
                    throw ValidationException::withMessages([
                        'quantite' =>
                            'La quantité consommée doit être strictement positive.',
                    ]);
                }

                $reste = $ligne->quantiteRestante();

                if ($quantite > $reste) {
                    throw ValidationException::withMessages([
                        'quantite' => sprintf(
                            'La quantité consommée (%s) dépasse le solde réservé (%s).',
                            $quantite,
                            $reste
                        ),
                    ]);
                }

                $ligne->quantite_consommee = round(
                    (float) $ligne->quantite_consommee + $quantite,
                    3
                );
                $ligne->save();
            }

            /*
             * Rechargement pour calculer le nouveau statut.
             */
            $reservation->load('lignes');

            $entierementConsommee = $reservation->lignes->every(
                fn (LigneReservationStock $l) => $l->estEntierementConsommee()
            );

            $auMoinsUnePartielle = $reservation->lignes->contains(
                fn (LigneReservationStock $l) => $l->estPartiellementConsommee()
                    || $l->estEntierementConsommee()
            );

            $reservation->update([
                'statut' => $entierementConsommee
                    ? StatutReservation::CONSOMMEE->value
                    : ($auMoinsUnePartielle
                        ? StatutReservation::PARTIELLE->value
                        : StatutReservation::CONFIRMEE->value),
            ]);

            return $reservation->fresh([
                'lignes.stock',
                'demandeur',
                'projet',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    /**
     * Annule une réservation.
     *
     * L'annulation libère immédiatement les quantités
     * puisqu'une réservation annulée n'est plus active.
     *
     * @throws Throwable
     */
    public function annuler(ReservationStock $reservation): ReservationStock
    {
        return DB::transaction(function () use ($reservation) {

            $reservation = ReservationStock::query()
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if (!$reservation->peutEtreAnnulee()) {
                throw ValidationException::withMessages([
                    'reservation' => sprintf(
                        'Une réservation "%s" ne peut pas être annulée.',
                        $reservation->statut->libelle()
                    ),
                ]);
            }

            $reservation->update([
                'statut' => StatutReservation::ANNULEE->value,
            ]);

            return $reservation->fresh([
                'lignes.stock',
                'demandeur',
                'projet',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | EXPIRATION
    |--------------------------------------------------------------------------
    */

    /**
     * Expire une réservation arrivée à échéance.
     *
     * @throws Throwable
     */
    public function expirer(ReservationStock $reservation): ReservationStock
    {
        return DB::transaction(function () use ($reservation) {

            $reservation = ReservationStock::query()
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if (!$reservation->peutEtreExpiree()) {
                throw ValidationException::withMessages([
                    'reservation' =>
                        'Cette réservation ne peut pas être expirée dans son état actuel.',
                ]);
            }

            if (!$reservation->date_expiration) {
                throw ValidationException::withMessages([
                    'date_expiration' =>
                        'Cette réservation ne possède pas de date d’expiration.',
                ]);
            }

            if (now()->lt($reservation->date_expiration)) {
                throw ValidationException::withMessages([
                    'date_expiration' =>
                        'La date d’expiration n’est pas encore atteinte.',
                ]);
            }

            $reservation->update([
                'statut' => StatutReservation::EXPIREE->value,
            ]);

            return $reservation->fresh([
                'lignes.stock',
                'demandeur',
                'projet',
            ]);
        });
    }

    /**
     * Expire automatiquement toutes les réservations échues.
     *
     * Utilisée par une commande Artisan planifiée.
     *
     * @return int Nombre de réservations expirées.
     */
    public function expirerReservationsEchues(): int
    {
        $count = 0;

        ReservationStock::query()
            ->actives()
            ->whereNotNull('date_expiration')
            ->whereDate('date_expiration', '<', now()->toDateString())
            ->orderBy('id')
            ->chunkById(100, function (Collection $reservations) use (&$count) {

                foreach ($reservations as $reservation) {
                    try {
                        $this->expirer($reservation);
                        $count++;
                    } catch (Throwable $e) {
                        /*
                         * On continue même si une réservation échoue :
                         * la commande doit traiter le maximum de cas
                         * valides. L'échec est journalisable par l'appelant.
                         */
                        report($e);
                    }
                }
            });

        return $count;
    }

    /*
    |--------------------------------------------------------------------------
    | LECTURES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Réservations actives d'un article.
     */
    public function reservationsActivesPourStock(int $stockId): Collection
    {
        return ReservationStock::query()
            ->actives()
            ->whereHas('lignes', function (Builder $q) use ($stockId) {
                $q->where('stock_id', $stockId);
            })
            ->with([
                'lignes' => fn ($q) => $q->where('stock_id', $stockId),
                'demandeur',
                'projet',
            ])
            ->orderBy('date_reservation')
            ->orderBy('id')
            ->get();
    }

    /**
     * Quantité actuellement réservée pour un article.
     */
    public function quantiteReservee(Stock $stock): float
    {
        $total = LigneReservationStock::query()
            ->where('stock_id', $stock->id)
            ->whereHas('reservation', function (Builder $q) {
                $q->whereIn('statut', StatutReservation::statutsActifs());
            })
            ->selectRaw(
                'COALESCE(SUM(quantite_reservee - quantite_consommee), 0) AS total'
            )
            ->value('total');

        return round((float) ($total ?? 0), 3);
    }

    /**
     * Quantité disponible d'un article pour une nouvelle réservation.
     */
    public function quantiteDisponible(Stock $stock): float
    {
        $physique = $stock->quantitePhysique();

        return max(0.0, $physique - $this->quantiteReservee($stock));
    }

    /**
     * Vérifie qu'une quantité peut être réservée pour un article.
     *
     * @throws ValidationException
     */
    public function verifierDisponibilite(
        Stock $stock,
        float $quantite
    ): void {
        if ($quantite <= 0) {
            throw ValidationException::withMessages([
                'quantite' =>
                    'La quantité à réserver doit être strictement positive.',
            ]);
        }

        $disponible = $this->quantiteDisponible($stock);

        if ($quantite > $disponible) {
            throw ValidationException::withMessages([
                'quantite' => sprintf(
                    'Stock insuffisant pour "%s". Disponible : %s, demandé : %s.',
                    $stock->designation,
                    $disponible,
                    $quantite
                ),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION DES LIGNES
    |--------------------------------------------------------------------------
    */

    /**
     * Crée une ligne de réservation.
     */
    private function creerLigne(
        ReservationStock $reservation,
        array $data
    ): LigneReservationStock {
        $stockId  = (int) ($data['stock_id'] ?? 0);
        $quantite = $this->decimal($data['quantite_demandee'] ?? 0);

        if ($stockId <= 0) {
            throw ValidationException::withMessages([
                'stock_id' => 'L’article est obligatoire.',
            ]);
        }

        if ($quantite <= 0) {
            throw ValidationException::withMessages([
                'quantite_demandee' =>
                    'La quantité demandée doit être strictement positive.',
            ]);
        }

        $stock = Stock::query()->find($stockId);

        if (!$stock) {
            throw ValidationException::withMessages([
                'stock_id' => sprintf('L’article #%d est introuvable.', $stockId),
            ]);
        }

        if (!$stock->peutEtreUtilise()) {
            throw ValidationException::withMessages([
                'stock_id' => sprintf(
                    'L’article "%s" est %s et ne peut pas être réservé.',
                    $stock->designation,
                    $stock->statut->libelle()
                ),
            ]);
        }

        return LigneReservationStock::create([
            'reservation_stock_id' => $reservation->id,
            'stock_id'             => $stock->id,
            'quantite_demandee'    => $quantite,
            'quantite_reservee'    => 0,
            'quantite_consommee'   => 0,
            'unite'                => $data['unite'] ?? $stock->unite,
            'notes'                => $data['notes'] ?? null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION DES LIGNES
    |--------------------------------------------------------------------------
    */

    /**
     * Valide les lignes reçues avant création.
     */
    private function validerLignes(array $lignes): void
    {
        $stocksVus = [];

        foreach ($lignes as $index => $ligne) {

            if (!is_array($ligne)) {
                throw ValidationException::withMessages([
                    "lignes.{$index}" => 'La ligne est invalide.',
                ]);
            }

            $stockId  = $ligne['stock_id'] ?? null;
            $quantite = $ligne['quantite_demandee'] ?? null;

            if (!$stockId) {
                throw ValidationException::withMessages([
                    "lignes.{$index}.stock_id" =>
                        'L’article est obligatoire.',
                ]);
            }

            if (!is_numeric($quantite) || (float) $quantite <= 0) {
                throw ValidationException::withMessages([
                    "lignes.{$index}.quantite_demandee" =>
                        'La quantité doit être un nombre strictement positif.',
                ]);
            }

            if (isset($stocksVus[$stockId])) {
                throw ValidationException::withMessages([
                    "lignes.{$index}.stock_id" =>
                        'Un article ne peut apparaître qu’une seule fois.',
                ]);
            }

            $stocksVus[$stockId] = true;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UTILITAIRES PRIVÉS
    |--------------------------------------------------------------------------
    */

    /**
     * Génère un numéro unique.
     *
     * Format : RES-YYYYMMDD-NNNN
     */
    private function genererNumero(): string
    {
        $prefixe = 'RES-' . now()->format('Ymd') . '-';

        $dernier = ReservationStock::query()
            ->where('numero', 'like', $prefixe . '%')
            ->orderByDesc('id')
            ->value('numero');

        if (!$dernier) {
            return $prefixe . '0001';
        }

        $sequence = (int) substr($dernier, strlen($prefixe));

        return $prefixe . str_pad(
            (string) ($sequence + 1),
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Convertit une valeur en décimal robuste.
     */
    private function decimal(mixed $value): float
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = str_replace(["\xc2\xa0", ' '], '', $value);

            if (str_contains($value, ',') && str_contains($value, '.')) {
                if (strrpos($value, ',') > strrpos($value, '.')) {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '.', $value);
                } else {
                    $value = str_replace(',', '', $value);
                }
            } elseif (str_contains($value, ',')) {
                $value = str_replace(',', '.', $value);
            }
        }

        return round((float) $value, 3);
    }
}