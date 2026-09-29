<?php

namespace App\Enums;

/**
 * Statut opérationnel d'un lot de stock.
 *
 * SÉMANTIQUE :
 *
 *   Le statut d'un lot reflète son état RÉEL et OPÉRATIONNEL.
 *   Il est maintenu par LotStockService, sous transaction,
 *   à chaque événement qui modifie la quantité ou la date
 *   d'expiration du lot.
 *
 *   C'est LUI qui détermine si un lot peut être sélectionné
 *   pour une sortie. Un lot dans un statut autre que ACTIF
 *   est exclu des sélections FEFO.
 *
 * CYCLE DE VIE (simplifié) :
 *
 *              ┌──────────────────────────┐
 *              │                          │
 *              ▼                          │
 *   ┌──────► actif ◄──── debloquer ◄── bloque
 *   │         │                          ▲
 *   │         │ quantité = 0             │
 *   │         ▼                          │
 *   │      epuise                        │
 *   │         │                          │
 *   │         │ nouvelle entrée          │
 *   │         └──────────────────────────┘
 *   │
 *   │         date dépassée
 *   └─── expire ◄──────── (prioritaire sur tout)
 *
 * RÈGLES :
 *
 *   - `expire` est PRIORITAIRE sur tout autre statut.
 *     Un lot expiré reste expiré, même s'il a de la quantité.
 *
 *   - `epuise` signifie quantité actuelle = 0.
 *
 *   - `bloque` est un état VOLONTAIRE (décision humaine).
 *     Un lot bloqué ne peut pas être sélectionné pour une
 *     sortie normale.
 *
 *   - Un lot bloqué ne peut pas redevenir actif automatiquement.
 *     Il doit être explicitement débloqué par un utilisateur.
 */
enum StatutLot: string
{
    /**
     * Lot actif : utilisable pour les sorties normales.
     */
    case ACTIF = 'actif';

    /**
     * Lot épuisé : quantité actuelle = 0.
     *
     * Conservé pour la traçabilité historique.
     */
    case EPUISE = 'epuise';

    /**
     * Lot expiré : date d'expiration dépassée.
     *
     * Exclu des sélections FEFO et de toute sortie normale,
     * même s'il possède encore une quantité positive.
     */
    case EXPIRE = 'expire';

    /**
     * Lot bloqué : interdit volontairement.
     *
     * Cas typiques :
     *   - suspicion de qualité ;
     *   - contrôle en cours ;
     *   - décision administrative ;
     *   - litige fournisseur.
     *
     * Il reste physiquement présent et compté dans le stock
     * physique, mais exclu des sorties normales.
     */
    case BLOQUE = 'bloque';

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ
    |--------------------------------------------------------------------------
    */

    public function libelle(): string
    {
        return match ($this) {
            self::ACTIF  => 'Actif',
            self::EPUISE => 'Épuisé',
            self::EXPIRE => 'Expiré',
            self::BLOQUE => 'Bloqué',
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

    public function estEpuise(): bool
    {
        return $this === self::EPUISE;
    }

    public function estExpire(): bool
    {
        return $this === self::EXPIRE;
    }

    public function estBloque(): bool
    {
        return $this === self::BLOQUE;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTORISATIONS MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Le lot peut-il être sélectionné pour une sortie normale ?
     *
     * ATTENTION :
     *   Cette méthode ne vérifie QUE le statut. La vérification
     *   de la quantité et de la date réelle est faite par
     *   LotStockService::estDisponible() ou par le scope
     *   `disponibles()` du modèle.
     */
    public function peutEtreUtilise(): bool
    {
        return $this === self::ACTIF;
    }

    /**
     * Le lot est-il indisponible pour une sortie normale ?
     *
     * Équivaut à `!peutEtreUtilise()`, mais exprimé
     * positivement pour la lisibilité métier.
     */
    public function estIndisponible(): bool
    {
        return $this !== self::ACTIF;
    }

    /**
     * Le lot peut-il être bloqué ?
     */
    public function peutEtreBloque(): bool
    {
        return $this === self::ACTIF;
    }

    /**
     * Le lot peut-il être débloqué ?
     */
    public function peutEtreDebloque(): bool
    {
        return $this === self::BLOQUE;
    }

    /**
     * Le lot est-il dans un état terminal (aucune transition
     * automatique possible) ?
     *
     * EXPIRE et EPUISE sont terminaux : ils ne repassent
     * pas en ACTIF automatiquement (un lot EPUISE peut redevenir
     * ACTIF uniquement via une nouvelle entrée, gérée par
     * EntreeStockService).
     */
    public function estTerminal(): bool
    {
        return in_array($this, [
            self::EXPIRE,
            self::EPUISE,
        ], true);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Statuts sélectionnables pour une sortie (ou toute
     * sélection FEFO).
     *
     * @return array<int, string>
     */
    public static function statutsDisponibles(): array
    {
        return [self::ACTIF->value];
    }

    /**
     * Tous les statuts sous forme de tableau associatif
     * `valeur => libellé`.
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