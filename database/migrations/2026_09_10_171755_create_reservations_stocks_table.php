<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Réservations de stock.
     *
     * Une réservation BLOQUE une quantité pour un usage futur,
     * SANS effectuer de sortie physique.
     *
     * Elle est portée par un workflow :
     *
     *   brouillon → confirmee → partielle → consommee
     *                     │           │
     *                     └→ expiree  └→ annulee
     *
     * Définitions :
     *
     *   Quantité physique   = somme des lots actifs/bloqués
     *   Quantité réservée   = somme des lignes réservées actives
     *   Quantité disponible = physique − réservée
     *
     * IMPORTANT :
     * Une réservation confirmée ou partielle est CONSIDÉRÉE
     * COMME ACTIVE : sa quantité est comptée dans le calcul
     * de la disponibilité, et son statut participe à la
     * protection contre la double attribution.
     */
    public function up(): void
    {
        Schema::create('reservations_stocks', function (Blueprint $table) {

            $table->id();

            /*
             * Numéro métier unique.
             *
             * Format généré côté service :
             * RES-YYYYMMDD-XXXX
             */
            $table->string('numero', 50)
                ->unique()
                ->comment('Numéro unique de la réservation');

            /*
             * -----------------------------------------------------------------
             * ACTEURS ET CONTEXTE
             * -----------------------------------------------------------------
             */

            /*
             * Utilisateur qui a créé la réservation.
             */
            $table->foreignId('demandeur_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur ayant créé la réservation');

            /*
             * Projet éventuellement associé.
             *
             * nullOnDelete : si le projet est supprimé, on conserve
             * la réservation pour la traçabilité.
             */
            $table->foreignId('projet_id')
                ->nullable()
                ->constrained('projets')
                ->nullOnDelete()
                ->comment('Projet associé à la réservation');

            /*
             * -----------------------------------------------------------------
             * DATES
             * -----------------------------------------------------------------
             */

            /*
             * Date de création de la réservation.
             */
            $table->date('date_reservation')
                ->comment('Date de création de la réservation');

            /*
             * Date limite de validité.
             *
             * Nullable : toutes les réservations n'ont pas
             * forcément une échéance (réservations internes).
             */
            $table->date('date_expiration')
                ->nullable()
                ->comment('Date limite de validité');

            /*
             * -----------------------------------------------------------------
             * WORKFLOW
             * -----------------------------------------------------------------
             */

            $table->enum('statut', [
                'brouillon',
                'confirmee',
                'partielle',
                'consommee',
                'expiree',
                'annulee',
            ])
                ->default('brouillon')
                ->comment('État du workflow de la réservation');

            /*
             * -----------------------------------------------------------------
             * CONTEXTE MÉTIER
             * -----------------------------------------------------------------
             */

            /*
             * Bénéficiaire prévu.
             */
            $table->string('destinataire', 255)
                ->nullable()
                ->comment('Bénéficiaire ou destinataire prévu');

            /*
             * Motif de la réservation.
             */
            $table->string('motif', 500)
                ->nullable()
                ->comment('Motif de la réservation');

            /*
             * Notes complémentaires.
             */
            $table->text('notes')
                ->nullable()
                ->comment('Informations complémentaires');

            $table->timestamps();

            /*
             * -----------------------------------------------------------------
             * INDEX
             * -----------------------------------------------------------------
             *
             * Les index FK (demandeur_id, projet_id) sont créés
             * automatiquement par les contraintes.
             */

            /*
             * Index sur date_reservation : filtre classique
             * pour les listes et rapports.
             */
            $table->index('date_reservation');

            /*
             * Index sur date_expiration : utilisé par
             * expirerReservationsEchues() pour trouver
             * les réservations à échéance.
             */
            $table->index('date_expiration');

            /*
             * Index sur statut : filtre principal.
             */
            $table->index('statut');

            /*
             * Index composé pour LA requête critique :
             *
             *   WHERE statut IN ('confirmee', 'partielle')
             *     AND date_expiration <= now()
             *
             * Utilisée par la commande d'expiration automatique
             * ET par les calculs de disponibilité.
             */
            $table->index(
                ['statut', 'date_expiration'],
                'reservations_stocks_actives_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations_stocks');
    }
};