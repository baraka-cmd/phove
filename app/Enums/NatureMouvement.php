<?php

namespace App\Enums;

/**
 * Nature métier d'un mouvement de stock.
 *
 * SÉMANTIQUE :
 *
 *   La NATURE explique POURQUOI le mouvement a eu lieu.
 *   Elle est volontairement séparée de `TypeMouvement`
 *   qui décrit COMMENT le mouvement impacte le stock.
 *
 *   Un mouvement a TOUJOURS un type ET une nature.
 *
 * TABLEAU DE CORRESPONDANCE (indicatif) :
 *
 *   Type          Nature usuelle
 *   ─────────────────────────────────────────────
 *   ENTREE        ACHAT, DON, RETOUR
 *   SORTIE        DISTRIBUTION, PERTE, PEREMPTION
 *   AJUSTEMENT    INVENTAIRE, CORRECTION
 *   TRANSFERT     TRANSFERT_INTERNE
 *
 *   Cette table est INDICATIVE :
 *   - la validation stricte est faite par les Services ;
 *   - le couple (type, nature) est vérifié à l'écriture.
 *
 * IMPORTANT :
 *
 *   Les méthodes estEntree(), estSortie(), etc. de cet enum
 *   sont INDICATIVES. Elles aident à classer les natures
 *   par sens probable, mais ne remplacent PAS la
 *   vérification métier des Services.
 */
enum NatureMouvement: string
{
    /*
    |--------------------------------------------------------------------------
    | ENTRÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Entrée provenant d'un achat.
     */
    case ACHAT = 'achat';

    /**
     * Entrée provenant d'un don.
     *
     * Fréquent dans le contexte humanitaire : dons
     * d'ONG, d'organismes internationaux, de particuliers.
     */
    case DON = 'don';

    /**
     * Retour d'un article précédemment sorti.
     */
    case RETOUR = 'retour';

    /*
    |--------------------------------------------------------------------------
    | SORTIES
    |--------------------------------------------------------------------------
    */

    /**
     * Distribution à un bénéficiaire, une structure,
     * ou dans le cadre d'une activité.
     */
    case DISTRIBUTION = 'distribution';

    /**
     * Perte physique constatée.
     *
     * Cas typiques :
     *   - casse ;
     *   - détérioration ;
     *   - disparition constatée.
     */
    case PERTE = 'perte';

    /**
     * Péremption d'un produit.
     */
    case PEREMPTION = 'peremption';

    /*
    |--------------------------------------------------------------------------
    | AJUSTEMENTS
    |--------------------------------------------------------------------------
    */

    /**
     * Ajustement résultant d'un inventaire physique.
     *
     * C'est la nature utilisée par InventaireStockService
     * lors de la validation d'un inventaire avec écart.
     */
    case INVENTAIRE = 'inventaire';

    /**
     * Correction exceptionnelle validée.
     *
     * Utilisée manuellement lorsqu'une régularisation
     * est nécessaire hors inventaire (erreur de saisie,
     * correction administrative).
     */
    case CORRECTION = 'correction';

    /*
    |--------------------------------------------------------------------------
    | TRANSFERTS
    |--------------------------------------------------------------------------
    */

    /**
     * Déplacement interne entre deux emplacements.
     */
    case TRANSFERT_INTERNE = 'transfert_interne';

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ
    |--------------------------------------------------------------------------
    */

    public function libelle(): string
    {
        return match ($this) {
            self::ACHAT             => 'Achat',
            self::DON               => 'Don',
            self::RETOUR            => 'Retour',
            self::DISTRIBUTION      => 'Distribution',
            self::PERTE             => 'Perte',
            self::PEREMPTION        => 'Péremption',
            self::INVENTAIRE        => 'Inventaire',
            self::CORRECTION        => 'Correction',
            self::TRANSFERT_INTERNE => 'Transfert interne',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estAchat(): bool
    {
        return $this === self::ACHAT;
    }

    public function estDon(): bool
    {
        return $this === self::DON;
    }

    public function estRetour(): bool
    {
        return $this === self::RETOUR;
    }

    public function estDistribution(): bool
    {
        return $this === self::DISTRIBUTION;
    }

    public function estPerte(): bool
    {
        return $this === self::PERTE;
    }

    public function estPeremption(): bool
    {
        return $this === self::PEREMPTION;
    }

    public function estInventaire(): bool
    {
        return $this === self::INVENTAIRE;
    }

    public function estCorrection(): bool
    {
        return $this === self::CORRECTION;
    }

    public function estTransfertInterne(): bool
    {
        return $this === self::TRANSFERT_INTERNE;
    }

    /*
    |--------------------------------------------------------------------------
    | CLASSIFICATION INDICATIVE
    |--------------------------------------------------------------------------
    |
    | Ces méthodes aident à classer les natures par "famille"
    | de mouvement. Elles sont INDICATIVES : la validation
    | stricte du couple (type, nature) est faite par les
    | Services métier.
    */

    /**
     * Cette nature correspond-elle habituellement à une entrée ?
     */
    public function estEntree(): bool
    {
        return match ($this) {
            self::ACHAT,
            self::DON,
            self::RETOUR            => true,

            default                 => false,
        };
    }

    /**
     * Cette nature correspond-elle habituellement à une sortie ?
     */
    public function estSortie(): bool
    {
        return match ($this) {
            self::DISTRIBUTION,
            self::PERTE,
            self::PEREMPTION        => true,

            default                 => false,
        };
    }

    /**
     * Cette nature correspond-elle à un ajustement ?
     */
    public function estAjustement(): bool
    {
        return match ($this) {
            self::INVENTAIRE,
            self::CORRECTION        => true,

            default                 => false,
        };
    }

    /**
     * Cette nature correspond-elle à un transfert ?
     */
    public function estTransfert(): bool
    {
        return $this === self::TRANSFERT_INTERNE;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Natures compatibles avec une entrée de stock.
     *
     * Utilisé par EntreeStockService pour valider les données.
     *
     * @return array<int, string>
     */
    public static function naturesEntree(): array
    {
        return [
            self::ACHAT->value,
            self::DON->value,
            self::RETOUR->value,
        ];
    }

    /**
     * Natures compatibles avec une sortie de stock.
     *
     * @return array<int, string>
     */
    public static function naturesSortie(): array
    {
        return [
            self::DISTRIBUTION->value,
            self::PERTE->value,
            self::PEREMPTION->value,
        ];
    }

    /**
     * Natures compatibles avec un ajustement.
     *
     * @return array<int, string>
     */
    public static function naturesAjustement(): array
    {
        return [
            self::INVENTAIRE->value,
            self::CORRECTION->value,
        ];
    }

    /**
     * Natures compatibles avec un transfert.
     *
     * @return array<int, string>
     */
    public static function naturesTransfert(): array
    {
        return [
            self::TRANSFERT_INTERNE->value,
        ];
    }

    /**
     * Toutes les natures sous forme `valeur => libellé`.
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