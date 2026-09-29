<?php

namespace App\Enums;

/**
 * Statut d'un bon de sortie.
 *
 * SÉMANTIQUE :
 *
 *   Un bon de sortie est le document métier qui porte le
 *   workflow de sortie physique d'articles. Il ne modifie
 *   JAMAIS directement les quantités.
 *
 *   C'est SortieStockService::effectuer() qui, à l'état
 *   EN_PREPARATION, génère les mouvements de sortie et
 *   décrémente les lots, sous transaction.
 *
 * CYCLE DE VIE NOMINAL :
 *
 *   brouillon
 *       │ (soumettre)
 *       ▼
 *   soumis
 *       │ (valider)
 *       ▼
 *   valide
 *       │ (mettre en préparation)
 *       ▼
 *   en_preparation
 *       │ (effectuer)
 *       ▼
 *   sorti  (terminal)
 *
 * Depuis BROUILLON, SOUMIS ou VALIDE, un bon peut
 * également être annulé :
 *
 *   annule  (terminal)
 *
 * RÈGLES :
 *
 *   - SORTI et ANNULE sont TERMINAUX : un bon terminé
 *     ne peut plus changer d'état.
 *
 *   - Un bon ne peut être exécuté QUE depuis EN_PREPARATION.
 *
 *   - Un bon ne peut être modifié QUE en BROUILLON
 *     ou SOUMIS.
 *
 *   - Un bon annulé conserve son historique :
 *     aucune suppression physique.
 */
enum StatutBonSortie: string
{
    /**
     * Bon en cours de rédaction.
     *
     * Aucune conséquence sur le stock.
     * Peut être modifié, soumis ou annulé.
     */
    case BROUILLON = 'brouillon';

    /**
     * Bon soumis pour validation.
     *
     * Toujours aucune conséquence sur le stock.
     * Peut être modifié, validé ou annulé.
     */
    case SOUMIS = 'soumis';

    /**
     * Bon validé par un utilisateur autorisé.
     *
     * La validation ne déclenche PAS encore de sortie
     * physique. Peut passer en préparation ou être annulé.
     */
    case VALIDE = 'valide';

    /**
     * Bon en cours de préparation physique.
     *
     * Le magasinier prépare les articles. Le Service
     * utilisera FEFO pour sélectionner les lots à
     * la sortie effective.
     */
    case EN_PREPARATION = 'en_preparation';

    /**
     * Sortie physiquement effectuée.
     *
     * Les mouvements de stock ont été créés et les lots
     * décrémentés. État terminal.
     */
    case SORTI = 'sorti';

    /**
     * Bon annulé.
     *
     * Conservé pour la traçabilité documentaire.
     * Aucune conséquence sur le stock.
     * État terminal.
     */
    case ANNULE = 'annule';

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ
    |--------------------------------------------------------------------------
    */

    public function libelle(): string
    {
        return match ($this) {
            self::BROUILLON      => 'Brouillon',
            self::SOUMIS         => 'Soumis',
            self::VALIDE         => 'Validé',
            self::EN_PREPARATION => 'En préparation',
            self::SORTI          => 'Sorti',
            self::ANNULE         => 'Annulé',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estBrouillon(): bool
    {
        return $this === self::BROUILLON;
    }

    public function estSoumis(): bool
    {
        return $this === self::SOUMIS;
    }

    public function estValide(): bool
    {
        return $this === self::VALIDE;
    }

    public function estEnPreparation(): bool
    {
        return $this === self::EN_PREPARATION;
    }

    public function estSorti(): bool
    {
        return $this === self::SORTI;
    }

    public function estAnnule(): bool
    {
        return $this === self::ANNULE;
    }

    /*
    |--------------------------------------------------------------------------
    | GROUPES MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Le bon est-il dans un état terminal ?
     */
    public function estTerminal(): bool
    {
        return $this === self::SORTI
            || $this === self::ANNULE;
    }

    /**
     * Le bon a-t-il déjà impacté le stock ?
     *
     * Seul SORTI implique que des mouvements de sortie
     * ont été créés.
     */
    public function aImpacteLeStock(): bool
    {
        return $this === self::SORTI;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTORISATIONS MÉTIER
    |--------------------------------------------------------------------------
    |
    | Ces méthodes exposent les règles génériques du workflow.
    | Le Service reste responsable de la vérification finale
    | dans une transaction (droits, disponibilité, etc.).
    */

    /**
     * Le bon peut-il être modifié (côté document) ?
     */
    public function peutEtreModifie(): bool
    {
        return $this === self::BROUILLON
            || $this === self::SOUMIS;
    }

    /**
     * Le bon peut-il être soumis ?
     */
    public function peutEtreSoumis(): bool
    {
        return $this === self::BROUILLON;
    }

    /**
     * Le bon peut-il être validé ?
     */
    public function peutEtreValide(): bool
    {
        return $this === self::SOUMIS;
    }

    /**
     * Le bon peut-il passer en préparation ?
     */
    public function peutEtrePrepare(): bool
    {
        return $this === self::VALIDE;
    }

    /**
     * Le bon peut-il être exécuté (sortie physique) ?
     *
     * Seul EN_PREPARATION autorise l'exécution.
     */
    public function peutEtreExecute(): bool
    {
        return $this === self::EN_PREPARATION;
    }

    /**
     * Le bon peut-il être annulé ?
     */
    public function peutEtreAnnule(): bool
    {
        return $this === self::BROUILLON
            || $this === self::SOUMIS
            || $this === self::VALIDE;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Statuts de workflow "en cours" (non terminaux).
     *
     * @return array<int, string>
     */
    public static function statutsEnCours(): array
    {
        return [
            self::SOUMIS->value,
            self::VALIDE->value,
            self::EN_PREPARATION->value,
        ];
    }

    /**
     * Tous les statuts sous forme `valeur => libellé`.
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
     * Valeurs brutes.
     *
     * @return array<int, string>
     */
    public static function valeurs(): array
    {
        return array_column(self::cases(), 'value');
    }
}