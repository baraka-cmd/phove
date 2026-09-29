<?php

namespace App\Enums;

/**
 * Type comptable d'un mouvement de stock.
 *
 * SÉMANTIQUE :
 *
 *   Le TYPE décrit la MÉCANIQUE COMPTABLE du mouvement,
 *   c'est-à-dire la façon dont il affecte la quantité
 *   physique du stock.
 *
 *   Il est volontairement séparé de `NatureMouvement` :
 *
 *     - `TypeMouvement`   → comment (mécanique)
 *     - `NatureMouvement` → pourquoi (motif métier)
 *
 *   Un mouvement a TOUJOURS un type et une nature.
 *
 * VALEURS :
 *
 *   entree     → augmente le stock          (sens +1)
 *   sortie     → diminue le stock           (sens -1)
 *   transfert  → déplace entre emplacements (sens 0)
 *   ajustement → corrige après inventaire   (sens +1 ou -1)
 *
 * IMPORTANT :
 *
 *   Le SENS réel du mouvement n'est PAS porté par cet enum.
 *   Il est porté par la colonne `sens` de `mouvements_stocks`,
 *   qui vaut :
 *
 *     +1 pour une entrée,
 *     -1 pour une sortie,
 *      0 pour un transfert (neutre global),
 *     +1 ou -1 pour un ajustement (surplus ou manquant).
 */
enum TypeMouvement: string
{
    /**
     * Entrée de stock.
     *
     * Augmente la quantité d'un lot, crée ou alimente un lot.
     *
     * Exemples de natures associées :
     *   - ACHAT
     *   - DON
     *   - RETOUR
     */
    case ENTREE = 'entree';

    /**
     * Sortie de stock.
     *
     * Diminue la quantité d'un lot, sélectionnée en FEFO
     * lorsque plusieurs lots sont concernés.
     *
     * Exemples de natures associées :
     *   - DISTRIBUTION
     *   - PERTE
     *   - PEREMPTION
     */
    case SORTIE = 'sortie';

    /**
     * Transfert interne entre emplacements.
     *
     * Le stock physique global ne change pas : la quantité
     * est déplacée d'un emplacement à un autre. Le mouvement
     * est matérialisé par DEUX lignes (une par emplacement).
     */
    case TRANSFERT = 'transfert';

    /**
     * Ajustement de stock.
     *
     * Utilisé notamment après un inventaire physique ou
     * une correction validée. Le sens dépend de l'écart :
     *
     *   +1 → surplus constaté
     *   -1 → manquant constaté
     */
    case AJUSTEMENT = 'ajustement';

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ
    |--------------------------------------------------------------------------
    */

    public function libelle(): string
    {
        return match ($this) {
            self::ENTREE     => 'Entrée',
            self::SORTIE     => 'Sortie',
            self::TRANSFERT  => 'Transfert',
            self::AJUSTEMENT => 'Ajustement',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estEntree(): bool
    {
        return $this === self::ENTREE;
    }

    public function estSortie(): bool
    {
        return $this === self::SORTIE;
    }

    public function estTransfert(): bool
    {
        return $this === self::TRANSFERT;
    }

    public function estAjustement(): bool
    {
        return $this === self::AJUSTEMENT;
    }

    /*
    |--------------------------------------------------------------------------
    | EFFET SUR LE STOCK
    |--------------------------------------------------------------------------
    */

    /**
     * Le mouvement augmente-t-il globalement le stock ?
     *
     * ATTENTION :
     *   Un AJUSTEMENT peut augmenter OU diminuer le stock
     *   selon le sens. Il n'est donc PAS considéré comme
     *   une augmentation automatique ici.
     */
    public function augmenteStock(): bool
    {
        return $this === self::ENTREE;
    }

    /**
     * Le mouvement diminue-t-il globalement le stock ?
     */
    public function diminueStock(): bool
    {
        return $this === self::SORTIE;
    }

    /**
     * Le mouvement est-il neutre globalement ?
     *
     * Un TRANSFERT ne change pas le total du stock
     * (il le déplace).
     */
    public function estNeutreGlobalement(): bool
    {
        return $this === self::TRANSFERT;
    }

    /**
     * Le mouvement peut-il porter un sens variable ?
     *
     * Un AJUSTEMENT peut être +1 ou -1 selon l'écart.
     */
    public function aSensVariable(): bool
    {
        return $this === self::AJUSTEMENT;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Tous les types sous forme `valeur => libellé`.
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