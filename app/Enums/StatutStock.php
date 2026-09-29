<?php

namespace App\Enums;

/**
 * Statut administratif d'un article de stock.
 *
 * SÉMANTIQUE :
 *
 *   Cet enum représente UNIQUEMENT l'état administratif
 *   du catalogue. Il ne reflète JAMAIS un niveau de quantité.
 *
 *   Les notions de quantité ("disponible", "faible", "rupture")
 *   sont des ÉTATS CALCULÉS, obtenus par les méthodes
 *   du modèle Stock (quantiteDisponible, estSousSeuil, etc.).
 *
 *   Ne jamais confondre :
 *
 *     - `statut` = état administratif (actif / inactif / archive)
 *     - état de stock = niveau de disponibilité (calculé)
 *
 * CYCLE DE VIE :
 *
 *   actif  ⇄  inactif   (désactivation / réactivation)
 *     │           │
 *     └────┬──────┘
 *          ▼
 *       archive  (définitif)
 *
 * Un article archivé ne peut plus jamais être réactivé.
 */
enum StatutStock: string
{
    /**
     * Article actif.
     *
     * Utilisable dans les opérations courantes : entrées,
     * sorties, réservations, inventaires.
     */
    case ACTIF = 'actif';

    /**
     * Article temporairement désactivé.
     *
     * Conservé pour la traçabilité, mais exclu des nouvelles
     * opérations (entrées, sorties, réservations). Il conserve
     * son historique et ses lots.
     */
    case INACTIF = 'inactif';

    /**
     * Article archivé (définitif).
     *
     * Retiré du catalogue actif. L'article et son historique
     * restent lisibles, mais il n'apparaît plus dans les
     * opérations courantes.
     *
     * L'archivage n'est autorisé que lorsque :
     *   - la quantité physique est nulle ;
     *   - la quantité réservée est nulle.
     */
    case ARCHIVE = 'archive';

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé destiné à l'interface utilisateur.
     */
    public function libelle(): string
    {
        return match ($this) {
            self::ACTIF   => 'Actif',
            self::INACTIF => 'Inactif',
            self::ARCHIVE => 'Archivé',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estActif(): bool
    {
        return $this === self::ACTIF;
    }

    public function estInactif(): bool
    {
        return $this === self::INACTIF;
    }

    public function estArchive(): bool
    {
        return $this === self::ARCHIVE;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTORISATIONS MÉTIER
    |--------------------------------------------------------------------------
    |
    | Ces méthodes exposent les règles génériques liées au statut.
    | Les Services restent responsables de la vérification finale
    | dans une transaction.
    */

    /**
     * L'article peut-il participer à une nouvelle opération
     * (entrée, sortie, réservation, inventaire) ?
     *
     * Seul un article ACTIF peut être utilisé.
     */
    public function peutEtreUtilise(): bool
    {
        return $this === self::ACTIF;
    }

    /**
     * L'article peut-il être désactivé ?
     */
    public function peutEtreDesactive(): bool
    {
        return $this === self::ACTIF;
    }

    /**
     * L'article peut-il être réactivé ?
     */
    public function peutEtreReactive(): bool
    {
        return $this === self::INACTIF;
    }

    /**
     * L'article peut-il être archivé ?
     *
     * Conditions de cohérence (quantités physiques/réservées)
     * vérifiées par StockService::archiver().
     */
    public function peutEtreArchive(): bool
    {
        return $this !== self::ARCHIVE;
    }

    /**
     * L'article est-il dans un état terminal ?
     */
    public function estTerminal(): bool
    {
        return $this === self::ARCHIVE;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Tous les statuts sous forme de tableau associatif
     * `valeur => libellé`.
     *
     * Utile pour les selects HTML, filtres, etc.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->libelle(),
            ])
            ->all();
    }

    /**
     * Valeurs brutes de l'enum.
     *
     * @return array<int, string>
     */
    public static function valeurs(): array
    {
        return array_column(self::cases(), 'value');
    }
}