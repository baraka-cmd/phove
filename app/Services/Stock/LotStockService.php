<?php

namespace App\Services\Stock;

use App\Enums\StatutLot;
use App\Models\LotStock;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

/**
 * Service métier des lots de stock.
 *
 * SÉMANTIQUE :
 *
 *   Le lot est la brique qui porte RÉELLEMENT la quantité
 *   physique d'un article. C'est lui qui est manipulé
 *   lors des entrées (incrément) et des sorties (décrément
 *   FEFO).
 *
 *   Ce service gère :
 *     - la création administrative de lots (rare) ;
 *     - le blocage / déblocage ;
 *     - le marquage des lots expirés ;
 *     - la synchronisation du statut après une opération ;
 *     - la sélection FEFO (délégation possible).
 *
 * IMPORTANT :
 *
 *   La quantité d'un lot ne doit JAMAIS être modifiée par
 *   ce service. C'est le rôle exclusif de :
 *     - EntreeStockService (incrément via entrée) ;
 *     - SortieStockService (décrément via FEFO) ;
 *     - InventaireStockService (ajustement via écart).
 *
 *   Ce service ne modifie QUE le statut du lot.
 *
 * SÉLECTION FEFO :
 *
 *   La sélection réelle des lots pour une sortie est faite
 *   par SortieStockService::selectionnerLotsFEFO(). Ce service
 *   fournit un scope `fefo()` et un ordre canonique.
 */
class LotStockService
{
    /*
    |--------------------------------------------------------------------------
    | LECTURE / RECHERCHE
    |--------------------------------------------------------------------------
    */

    /**
     * Prépare la requête de recherche des lots.
     */
    public function requete(
        ?string $recherche = null,
        ?string $statut = null,
        ?int $stockId = null,
        ?bool $expires = null,
        ?bool $expirationProche = null
    ): Builder {
        return LotStock::query()
            ->with('stock')
            ->when(
                $stockId && $stockId > 0,
                fn (Builder $q) => $q->where('stock_id', $stockId)
            )
            ->when(
                $statut,
                fn (Builder $q, string $s) => $q->where('statut', $s)
            )
            ->when(
                $recherche,
                function (Builder $q, string $r) {
                    $r = trim($r);

                    $q->where(function (Builder $q) use ($r) {
                        $q->where('numero_lot', 'like', "%{$r}%")
                          ->orWhere('fournisseur', 'like', "%{$r}%")
                          ->orWhere('reference_document', 'like', "%{$r}%")
                          ->orWhereHas('stock', function (Builder $sq) use ($r) {
                              $sq->where('designation', 'like', "%{$r}%")
                                 ->orWhere('reference', 'like', "%{$r}%");
                          });
                    });
                }
            )
            ->when(
                $expires === true,
                fn (Builder $q) => $q->expires()
            )
            ->when(
                $expirationProche === true,
                fn (Builder $q) => $q->expirantBientot()
            );
    }

    /**
     * Récupère un lot par identifiant.
     */
    public function trouver(int $id): LotStock
    {
        return LotStock::query()
            ->with('stock')
            ->findOrFail($id);
    }

    /**
     * Récupère un lot par numéro au sein d'un article.
     */
    public function trouverParNumero(
        Stock $stock,
        string $numeroLot
    ): LotStock {
        return LotStock::query()
            ->where('stock_id', $stock->id)
            ->where('numero_lot', trim($numeroLot))
            ->firstOrFail();
    }

    /**
     * Lots disponibles d'un article (FEFO).
     */
    public function lotsDisponibles(Stock $stock): Collection
    {
        return LotStock::query()
            ->where('stock_id', $stock->id)
            ->disponibles()
            ->fefo()
            ->get();
    }

    /**
     * Lots actifs d'un article, triés FEFO.
     */
    public function lotsActifs(Stock $stock): Collection
    {
        return LotStock::query()
            ->where('stock_id', $stock->id)
            ->actifs()
            ->fefo()
            ->get();
    }

    /**
     * Lots expirant bientôt (défaut : 30 jours).
     */
    public function expirantBientot(int $jours = 30): Collection
    {
        if ($jours < 0) {
            throw new InvalidArgumentException(
                'Le nombre de jours ne peut pas être négatif.'
            );
        }

        return LotStock::query()
            ->with('stock')
            ->expirantBientot($jours)
            ->orderBy('date_expiration')
            ->orderBy('id')
            ->get();
    }

    /**
     * Lots dont la date d'expiration est dépassée.
     */
    public function expires(): Collection
    {
        return LotStock::query()
            ->with('stock')
            ->expires()
            ->orderBy('date_expiration')
            ->orderBy('id')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | BLOCAGE / DÉBLOCAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Bloque un lot.
     *
     * Un lot bloqué reste physiquement présent, mais
     * n'est plus sélectionnable pour une sortie normale.
     *
     * @throws Throwable
     */
    public function bloquer(LotStock $lot): LotStock
    {
        return DB::transaction(function () use ($lot) {

            $lot = LotStock::query()
                ->lockForUpdate()
                ->findOrFail($lot->id);

            if (!$lot->statut->peutEtreBloque()) {
                throw ValidationException::withMessages([
                    'statut' => sprintf(
                        'Un lot "%s" ne peut pas être bloqué.',
                        $lot->statut->libelle()
                    ),
                ]);
            }

            $lot->statut = StatutLot::BLOQUE;
            $lot->save();

            return $lot->fresh('stock');
        });
    }

    /**
     * Débloque un lot.
     *
     * Le statut est recalculé selon la réalité :
     *   - quantité <= 0 → EPUISE
     *   - date dépassée → EXPIRE
     *   - sinon         → ACTIF
     *
     * @throws Throwable
     */
    public function debloquer(LotStock $lot): LotStock
    {
        return DB::transaction(function () use ($lot) {

            $lot = LotStock::query()
                ->lockForUpdate()
                ->findOrFail($lot->id);

            if (!$lot->statut->peutEtreDebloque()) {
                throw ValidationException::withMessages([
                    'statut' => sprintf(
                        'Un lot "%s" ne peut pas être débloqué.',
                        $lot->statut->libelle()
                    ),
                ]);
            }

            $lot->statut = $this->determinerStatutApresOperation($lot);
            $lot->save();

            return $lot->fresh('stock');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | EXPIRATION
    |--------------------------------------------------------------------------
    */

    /**
     * Marque comme EXPIRE tous les lots dont la date est dépassée.
     *
     * Traite les lots ACTIF et BLOQUE.
     *
     * Utilisée par une commande Artisan planifiée.
     *
     * @return int Nombre de lots marqués.
     */
    public function traiterExpirations(): int
    {
        $nombre = 0;

        LotStock::query()
            ->whereNotNull('date_expiration')
            ->whereDate('date_expiration', '<', now()->toDateString())
            ->whereIn('statut', [
                StatutLot::ACTIF->value,
                StatutLot::BLOQUE->value,
            ])
            ->orderBy('id')
            ->chunkById(100, function (Collection $lots) use (&$nombre) {
                foreach ($lots as $lot) {
                    try {
                        DB::transaction(function () use ($lot, &$nombre) {

                            $lot = LotStock::query()
                                ->lockForUpdate()
                                ->find($lot->id);

                            if (!$lot) {
                                return;
                            }

                            /*
                             * Re-vérification après lock :
                             * le lot a pu changer entre la lecture initiale
                             * et l'obtention du verrou.
                             */
                            if (!$lot->estExpireSelonDate()) {
                                return;
                            }

                            if ($lot->estExpire()) {
                                return;
                            }

                            $lot->statut = StatutLot::EXPIRE;
                            $lot->save();

                            $nombre++;
                        });
                    } catch (Throwable $e) {
                        report($e);
                    }
                }
            });

        return $nombre;
    }

        /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    |
    | Comptage global des lots par état.
    |
    | Clés retournées (alignées sur la vue lots/index.blade.php) :
    |   - total
    |   - disponibles
    |   - bloques
    |   - expires
    */

    /**
     * Retourne les statistiques des lots.
     *
     * @return array<string, int>
     */
    public function statistiques(): array
    {
        return [
            'total'       => LotStock::query()->count(),

            'disponibles' => LotStock::query()
                ->where('statut', StatutLot::ACTIF->value)
                ->where('quantite_actuelle', '>', 0)
                ->count(),

            'bloques'     => LotStock::query()
                ->where('statut', StatutLot::BLOQUE->value)
                ->count(),

            'expires'     => LotStock::query()
                ->where('statut', StatutLot::EXPIRE->value)
                ->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISATION DU STATUT
    |--------------------------------------------------------------------------
    */

    /**
     * Recalcule et enregistre le statut d'un lot après une
     * opération ayant modifié sa quantité ou sa date.
     *
     * ⚠️ Cette méthode NE modifie PAS la quantité.
     *    Elle ne fait que mettre à jour le statut.
     *
     * @throws Throwable
     */
    public function synchroniserStatut(LotStock $lot): LotStock
    {
        return DB::transaction(function () use ($lot) {

            $lot = LotStock::query()
                ->lockForUpdate()
                ->findOrFail($lot->id);

            $lot->statut = $this->determinerStatutApresOperation($lot);
            $lot->save();

            return $lot->fresh('stock');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le lot est-il utilisable pour une sortie ?
     */
    public function estDisponible(LotStock $lot): bool
    {
        return $lot->peutEtreUtilise();
    }

    /**
     * Quantité réellement disponible dans le lot.
     */
    public function quantiteDisponible(LotStock $lot): float
    {
        return round(max(0.0, (float) $lot->quantite_actuelle), 3);
    }

    /**
     * Quantité totale présente dans les lots d'un article.
     *
     * Inclut ACTIF et BLOQUE (les deux sont physiquement présents).
     */
    public function totalDisponible(Stock $stock): float
    {
        return round(
            (float) LotStock::query()
                ->where('stock_id', $stock->id)
                ->whereIn('statut', [
                    StatutLot::ACTIF->value,
                    StatutLot::BLOQUE->value,
                ])
                ->sum('quantite_actuelle'),
            3
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Détermine le statut correct d'un lot après une opération.
     *
     * Règles (par ordre de priorité) :
     *   1. quantité <= 0            → EPUISE
     *   2. date dépassée            → EXPIRE
     *   3. actuellement BLOQUE      → on conserve BLOQUE
     *   4. sinon                    → ACTIF
     *
     * ⚠️ Un lot BLOQUE n'est JAMAIS réactivé automatiquement.
     *    Il doit être explicitement débloqué.
     */
    private function determinerStatutApresOperation(LotStock $lot): StatutLot
    {
        if ((float) $lot->quantite_actuelle <= 0) {
            return StatutLot::EPUISE;
        }

        if ($lot->estExpireSelonDate()) {
            return StatutLot::EXPIRE;
        }

        if ($lot->estBloque()) {
            return StatutLot::BLOQUE;
        }

        return StatutLot::ACTIF;
    }
}