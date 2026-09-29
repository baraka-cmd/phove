<?php

namespace App\Enums;

/**
 * Définit les différents états d'un bon de sortie.
 *
 * Le bon de sortie représente le document métier permettant
 * de demander, valider, préparer puis effectuer une sortie
 * réelle de stock.
 *
 * Cycle nominal :
 *
 * brouillon
 *     ↓
 * soumis
 *     ↓
 * valide
 *     ↓
 * en_preparation
 *     ↓
 * sorti
 *
 * Un bon peut également être annulé selon les règles métier.
 */
enum StatutBonSortie: string
{
    /**
     * Bon en cours de rédaction.
     *
     * Aucune sortie physique ne doit encore être effectuée.
     */
    case BROUILLON = 'brouillon';

    /**
     * Bon soumis pour validation.
     */
    case SOUMIS = 'soumis';

    /**
     * Bon validé par une personne autorisée.
     *
     * La validation ne signifie pas encore que la marchandise
     * a physiquement quitté le magasin.
     */
    case VALIDE = 'valide';

    /**
     * Bon en cours de préparation.
     *
     * Le magasinier prépare les articles et sélectionne
     * notamment les lots selon la logique FEFO lorsque celle-ci
     * est applicable.
     */
    case EN_PREPARATION = 'en_preparation';

    /**
     * Sortie physiquement effectuée.
     *
     * Les mouvements de stock correspondants doivent alors
     * avoir été enregistrés de manière transactionnelle.
     */
    case SORTI = 'sorti';

    /**
     * Bon annulé.
     *
     * Le document est conservé pour la traçabilité.
     */
    case ANNULE = 'annule';

    /**
     * Retourne le libellé français destiné à l'interface.
     */
    public function libelle(): string
    {
        return match ($this) {
            self::BROUILLON => 'Brouillon',
            self::SOUMIS => 'Soumis',
            self::VALIDE => 'Validé',
            self::EN_PREPARATION => 'En préparation',
            self::SORTI => 'Sorti',
            self::ANNULE => 'Annulé',
        };
    }

    /**
     * Indique si le bon est un brouillon.
     */
    public function estBrouillon(): bool
    {
        return $this === self::BROUILLON;
    }

    /**
     * Indique si le bon a été soumis.
     */
    public function estSoumis(): bool
    {
        return $this === self::SOUMIS;
    }

    /**
     * Indique si le bon est validé.
     */
    public function estValide(): bool
    {
        return $this === self::VALIDE;
    }

    /**
     * Indique si le bon est actuellement en préparation.
     */
    public function estEnPreparation(): bool
    {
        return $this === self::EN_PREPARATION;
    }

    /**
     * Indique si la sortie physique a été effectuée.
     */
    public function estSorti(): bool
    {
        return $this === self::SORTI;
    }

    /**
     * Indique si le bon est annulé.
     */
    public function estAnnule(): bool
    {
        return $this === self::ANNULE;
    }

    /**
     * Indique si le bon est terminé.
     *
     * Un bon sorti ou annulé est considéré comme terminé
     * du point de vue du workflow.
     */
    public function estTermine(): bool
    {
        return match ($this) {
            self::SORTI,
            self::ANNULE => true,

            default => false,
        };
    }

    /**
     * Indique si le bon peut encore être modifié
     * comme document de travail.
     */
    public function peutEtreModifie(): bool
    {
        return $this === self::BROUILLON;
    }

    /**
     * Indique si le bon représente déjà une sortie physique.
     *
     * Important :
     * cette méthode ne remplace pas la vérification métier
     * du Service.
     */
    public function sortieEffectuee(): bool
    {
        return $this === self::SORTI;
    }

    /**
     * Retourne toutes les valeurs brutes de l'enum.
     */
    public static function valeurs(): array
    {
        return array_column(self::cases(), 'value');
    }
}