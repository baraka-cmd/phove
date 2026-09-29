<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des paiements de salaires.
     *
     * Un salaire peut recevoir plusieurs paiements.
     *
     * Exemple :
     *
     * Salaire de 600 $
     *
     * Paiement 1 : 200 $
     * Paiement 2 : 150 $
     * Paiement 3 : 250 $
     *
     * Total payé : 600 $
     *
     * Cette architecture permet donc de gérer :
     * - les paiements complets ;
     * - les paiements partiels ;
     * - les paiements en plusieurs tranches ;
     * - l'historique des paiements ;
     * - les différents modes de paiement ;
     * - la traçabilité de l'utilisateur ayant effectué le paiement.
     */
    public function up(): void
    {
        Schema::create('paiements_salaires', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | IDENTIFIANT
            |--------------------------------------------------------------------------
            */

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | SALAIRE CONCERNÉ
            |--------------------------------------------------------------------------
            |
            | Chaque paiement appartient obligatoirement à un salaire.
            |
            | Exemple :
            |
            | salaires.id = 25
            |
            | paiement :
            | salaire_id = 25
            |
            */

            $table->foreignId('salaire_id')
                ->constrained('salaires')
                ->restrictOnDelete()
                ->comment(
                    'Salaire auquel ce paiement est rattaché.'
                );


            /*
            |--------------------------------------------------------------------------
            | MONTANT DU PAIEMENT
            |--------------------------------------------------------------------------
            |
            | Le montant correspond uniquement à cette opération de paiement.
            |
            | Exemple :
            |
            | Salaire = 600
            |
            | Paiement = 250
            |
            | montant = 250.00
            |
            */

            $table->decimal('montant', 12, 2)
                ->comment(
                    'Montant effectivement versé lors de cette opération.'
                );


            /*
            |--------------------------------------------------------------------------
            | DATE DU PAIEMENT
            |--------------------------------------------------------------------------
            |
            | Il s'agit de la date réelle à laquelle l'argent a été versé.
            |
            | Elle est obligatoire pour garantir une bonne traçabilité
            | financière.
            |
            */

            $table->date('date_paiement')
                ->comment(
                    'Date effective du paiement.'
                );


            /*
            |--------------------------------------------------------------------------
            | MODE DE PAIEMENT
            |--------------------------------------------------------------------------
            |
            | Nous utilisons un VARCHAR plutôt qu'un ENUM.
            |
            | Pourquoi ?
            |
            | Parce qu'une organisation peut ajouter ultérieurement :
            |
            | - carte bancaire ;
            | - paiement électronique ;
            | - autre moyen institutionnel ;
            |
            | sans devoir modifier la structure de la base de données.
            |
            | Les valeurs actuellement autorisées seront contrôlées
            | au niveau des Form Requests / Services.
            |
            */

            $table->string('mode_paiement', 30)
                ->comment(
                    'Mode utilisé pour effectuer le paiement.'
                );


            /*
            |--------------------------------------------------------------------------
            | RÉFÉRENCE DU PAIEMENT
            |--------------------------------------------------------------------------
            |
            | Exemple :
            |
            | PAY-202609-0001
            |
            | Cette référence est différente de la référence du salaire.
            |
            | Salaire :
            | BUL-202609-0001
            |
            | Paiement :
            | PAY-202609-0001
            |
            */

            $table->string('reference', 50)
                ->unique()
                ->comment(
                    'Référence unique de l’opération de paiement.'
                );


            /*
            |--------------------------------------------------------------------------
            | UTILISATEUR AYANT EFFECTUÉ LE PAIEMENT
            |--------------------------------------------------------------------------
            |
            | On conserve l'identité de l'utilisateur responsable
            | de l'enregistrement du paiement.
            |
            | Exemple :
            |
            | Comptable A enregistre le paiement.
            |
            | paye_par = ID du Comptable A
            |
            */

            $table->foreignId('paye_par')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment(
                    'Utilisateur ayant enregistré ou effectué le paiement.'
                );


            /*
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            |
            | Permet de conserver des informations complémentaires.
            |
            | Exemples :
            |
            | - Paiement de la première tranche.
            | - Solde du mois précédent.
            | - Paiement effectué exceptionnellement en espèces.
            | - Référence transaction mobile money.
            |
            */

            $table->text('notes')
                ->nullable()
                ->comment(
                    'Observations ou informations complémentaires sur le paiement.'
                );


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            |
            | Ils permettent d'optimiser les requêtes utilisées dans :
            |
            | - l'historique d'un salaire ;
            | - les rapports financiers ;
            | - les statistiques mensuelles ;
            | - les recherches par date ;
            | - les recherches par mode de paiement ;
            | - les recherches par utilisateur.
            |
            */

            $table->index(
                'salaire_id',
                'paiements_salaires_salaire_id_index'
            );

            $table->index(
                'date_paiement',
                'paiements_salaires_date_paiement_index'
            );

            $table->index(
                'mode_paiement',
                'paiements_salaires_mode_paiement_index'
            );

            $table->index(
                'paye_par',
                'paiements_salaires_paye_par_index'
            );


            /*
            |--------------------------------------------------------------------------
            | INDEX COMPOSÉ
            |--------------------------------------------------------------------------
            |
            | Très utile pour les rapports financiers mensuels.
            |
            | Exemple :
            |
            | SELECT SUM(montant)
            | FROM paiements_salaires
            | WHERE date_paiement BETWEEN ...
            |
            */

            $table->index(
                ['date_paiement', 'mode_paiement'],
                'paiements_salaires_date_mode_index'
            );
        });
    }


    /**
     * Supprime la table des paiements de salaires.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements_salaires');
    }
};