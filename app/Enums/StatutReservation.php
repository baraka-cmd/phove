<?php

namespace App\Enums;

/**
 * Statut d'une réservation de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une réservation BLOQUE une quantité pour un usage futur,
 *   SANS effectuer de sortie physique. Tant qu'elle est
 *   ACTIVE, la quantité réservée est retirée de la
 *   disponibilité de l'article.
 *
 * CYCLE DE VIE :
 *
 *   brouillon
 *       │ (confirmer)
 *       ▼
 *   confirmee ◄──────────────┐
 *       │                     │
 *       │ (consommation       │
 *       │  partielle)         │
 *       ▼                     │
 *   partielle                 │
 *       │                     │
 *       │ (consommation       │
 *       │  totale)            │
 *       ▼                     │
 *   consommee                 │
 *                             │
 *   ┌─── expiree ─────────────┤
 *   │                         │
 *   └─── annulee ◄────────────┘
 *         (depuis brouillon,
 *          confirmee, partielle)
 *
 * RÈGLES :
 *
 *   - Seules CONFIRMEE et PARTIELLE sont ACTIVES :
 *     elles impactent la disponibilité.
 *
 *   - BROUILLON ne bloque rien : c'est une réservation
 *     en cours de saisie.
 *
 *   - CONSOMMEE, EXPIREE, ANNULEE sont TERMINALES :
 *     elles n'impactent plus la disponibilité.
 *
 *   - Une réservation terminée ne peut plus changer d'état.
 */
enum StatutReservation: string
{
    /**
     * Réservation en cours de saisie.
     *
     * Aucune quantité n'est encore bloquée.
     */
    case BROUILLON = 'brouillon';

    /**
     * Réservation confirmée.
     *
     * La quantité est effectivement bloquée et retirée
     * de la disponibilité. Elle peut encore être consommée
     * (partiellement ou totalement), annulée ou expirée.
     */
    case CONFIRMEE = 'confirmee';

    /**
     * Réservation partiellement consommée.
     *
     * Une partie a été convertie en sortie physique,
     * le solde restant reste réservé et bloqué.
     */
    case PARTIELLE = 'partielle';

    /**
     * Réservation entièrement consommée.
     *
     * Aucune quantité n'est plus réservée : tout a été
     * converti en sortie physique. État terminal.
     */
    case CONSOMMEE = 'consommee';

    /**
     * Réservation expirée.
     *
     * La date limite a été dépassée avant consommation
     * complète. La quantité encore réservée est libérée.
     * État terminal.
     */
    case EXPIREE = 'expiree';

    /**
     * Réservation annulée.
     *
     * Annulation volontaire avant consommation complète.
     * La quantité encore réservée est libérée.
     * État terminal.
     */
    case ANNULEE = 'annulee';

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ
    |--------------------------------------------------------------------------
    */

    public function libelle(): string
    {
        return match ($this) {
            self::BROUILLON  => 'Brouillon',
            self::CONFIRMEE  => 'Confirmée',
            self::PARTIELLE  => 'Partielle',
            self::CONSOMMEE  => 'Consommée',
            self::EXPIREE    => 'Expirée',
            self::ANNULEE    => 'Annulée',
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

    public function estConfirmee(): bool
    {
        return $this === self::CONFIRMEE;
    }

    public function estPartielle(): bool
    {
        return $this === self::PARTIELLE;
    }

    public function estConsommee(): bool
    {
        return $this === self::CONSOMMEE;
    }

    public function estExpiree(): bool
    {
        return $this === self::EXPIREE;
    }

    public function estAnnulee(): bool
    {
        return $this === self::ANNULEE;
    }

    /*
    |--------------------------------------------------------------------------
    | GROUPES MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * La réservation impacte-t-elle la disponibilité ?
     *
     * Seules CONFIRMEE et PARTIELLE sont actives.
     */
    public function estActive(): bool
    {
        return $this === self::CONFIRMEE
            || $this === self::PARTIELLE;
    }

    /**
     * La réservation est-elle dans un état terminal ?
     *
     * Un état terminal ne peut plus jamais changer.
     */
    public function estTerminale(): bool
    {
        return in_array($this, [
            self::CONSOMMEE,
            self::EXPIREE,
            self::ANNULEE,
        ], true);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTORISATIONS MÉTIER
    |--------------------------------------------------------------------------
    |
    | Ces méthodes exposent les règles génériques liées au statut.
    | Le Service reste responsable de la vérification finale
    | dans une transaction.
    */

    /**
     * La réservation peut-elle être modifiée (côté document) ?
     */
    public function peutEtreModifiee(): bool
    {
        return $this === self::BROUILLON;
    }

    /**
     * La réservation peut-elle être confirmée ?
     *
     * La confirmation est possible uniquement depuis BROUILLON.
     */
    public function peutEtreConfirmee(): bool
    {
        return $this === self::BROUILLON;
    }

    /**
     * La réservation peut-elle être consommée
     * (partiellement ou totalement) ?
     */
    public function peutEtreConsommee(): bool
    {
        return $this->estActive();
    }

    /**
     * La réservation peut-elle être annulée ?
     */
    public function peutEtreAnnulee(): bool
    {
        return !$this->estTerminale();
    }

    /**
     * La réservation peut-elle être expirée ?
     *
     * Nécessite qu'elle soit active ET que la date
     * d'expiration soit dépassée — la vérification de
     * la date reste à la charge du Service.
     */
    public function peutEtreExpiree(): bool
    {
        return $this->estActive();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Statuts impactant la disponibilité.
     *
     * @return array<int, string>
     */
    public static function statutsActifs(): array
    {
        return [
            self::CONFIRMEE->value,
            self::PARTIELLE->value,
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