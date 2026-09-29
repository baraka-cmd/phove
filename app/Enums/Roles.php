<?php

namespace App\Enums;

use InvalidArgumentException;

/**
 * Rôles hiérarchiques PHOVE.
 *
 * SÉMANTIQUE :
 *
 *   Cette classe centralise tous les rôles de l'application
 *   ainsi que :
 *     - leurs libellés français ;
 *     - leurs niveaux hiérarchiques ;
 *     - les regroupements fonctionnels (qui peut faire quoi).
 *
 * POURQUOI UNE CLASSE ET PAS UN ENUM ?
 *
 *   Les rôles PHOVE sont utilisés partout comme des CHAÎNES
 *   (dans les middleware, policies, validations, base de données).
 *
 *   Un enum PHP aurait obligé à appeler `->value` à chaque usage,
 *   ce qui aurait pollué tout le code existant.
 *
 *   Cette classe utilise donc des constantes de type string,
 *   ce qui garantit :
 *     - la cohérence (une seule source de vérité) ;
 *     - la compatibilité avec l'existant ;
 *     - la simplicité d'usage.
 *
 * UTILISATION TYPIQUE :
 *
 *   Roles::SUPER_ADMIN                       // 'super_admin'
 *   Roles::all()                             // [role => libellé]
 *   Roles::gestionStocks()                   // [roles autorisés]
 *   Roles::existe($role)                     // bool
 *   Roles::niveau($role)                     // int
 */
final class Roles
{
    /*
    |--------------------------------------------------------------------------
    | CONSTANTES DE RÔLES
    |--------------------------------------------------------------------------
    */

    public const SUPER_ADMIN       = 'super_admin';
    public const DG                = 'dg';
    public const CHEF_MISSION      = 'chef_mission';
    public const CHARGE_PROGRAMME  = 'charge_programme';
    public const SECRETAIRE        = 'secretaire';
    public const COMPTABLE         = 'comptable';
    public const CAISSIER          = 'caissier';
    public const EDITEUR           = 'editeur';
    public const UTILISATEUR       = 'utilisateur';

    /*
    |--------------------------------------------------------------------------
    | NIVEAUX HIÉRARCHIQUES
    |--------------------------------------------------------------------------
    |
    | Du plus haut (5) au plus bas (0).
    |
    | Les niveaux servent à comparer des rôles, jamais à
    | accorder automatiquement des droits (règle de sécurité).
    */

    public const NIVEAU_SUPER_ADMIN      = 5;
    public const NIVEAU_DG               = 4;
    public const NIVEAU_COORDINATION     = 3;
    public const NIVEAU_EXECUTION        = 2;
    public const NIVEAU_OPERATIONNEL     = 1;
    public const NIVEAU_CONSULTATION     = 0;

    /*
    |--------------------------------------------------------------------------
    | RÉFÉRENTIEL
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne tous les rôles avec leurs libellés.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN      => 'Super Administrateur',
            self::DG               => 'Directeur Général',
            self::CHEF_MISSION     => 'Chef de Mission',
            self::CHARGE_PROGRAMME => 'Chargé de Programme',
            self::SECRETAIRE       => 'Secrétaire Exécutif',
            self::COMPTABLE        => 'Comptable',
            self::CAISSIER         => 'Caissier',
            self::EDITEUR          => 'Éditeur',
            self::UTILISATEUR      => 'Utilisateur',
        ];
    }

    /**
     * Retourne la liste brute des rôles.
     *
     * Utile pour les validations `in:` et les tests.
     *
     * @return array<int, string>
     */
    public static function valeurs(): array
    {
        return array_keys(self::all());
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si un rôle existe.
     */
    public static function existe(string $role): bool
    {
        return array_key_exists($role, self::all());
    }

    /**
     * Vérifie qu'un rôle existe, et lance une exception sinon.
     *
     * Utilisation recommandée dans les Services pour éviter
     * les comportements silencieux.
     */
    public static function valider(string $role): void
    {
        if (!self::existe($role)) {
            throw new InvalidArgumentException(
                sprintf('Rôle inconnu : "%s".', $role)
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉS ET NIVEAUX
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le libellé français d'un rôle.
     *
     * Si le rôle est inconnu, on retourne le rôle brut.
     * Pour un contrôle strict, utiliser `valider()` avant.
     */
    public static function libelle(string $role): string
    {
        return self::all()[$role] ?? $role;
    }

    /**
     * Retourne le niveau hiérarchique d'un rôle.
     *
     * Renvoie -1 pour un rôle inconnu, afin de NE JAMAIS
     * donner un niveau positif par défaut.
     *
     * (L'ancienne version retournait 0, ce qui rendait
     * un rôle inconnu équivalent à UTILISATEUR → dangereux.)
     */
    public static function niveau(string $role): int
    {
        return match ($role) {
            self::SUPER_ADMIN      => self::NIVEAU_SUPER_ADMIN,
            self::DG               => self::NIVEAU_DG,
            self::CHEF_MISSION,
            self::CHARGE_PROGRAMME => self::NIVEAU_COORDINATION,
            self::SECRETAIRE,
            self::COMPTABLE        => self::NIVEAU_EXECUTION,
            self::CAISSIER,
            self::EDITEUR          => self::NIVEAU_OPERATIONNEL,
            self::UTILISATEUR      => self::NIVEAU_CONSULTATION,
            default                => -1,
        };
    }

    /**
     * Un rôle a-t-il un niveau supérieur ou égal à un autre ?
     *
     * Si l'un des rôles est inconnu, on renvoie `false`.
     */
    public static function superieurOuEgal(
        string $role1,
        string $role2
    ): bool {
        $niveau1 = self::niveau($role1);
        $niveau2 = self::niveau($role2);

        if ($niveau1 < 0 || $niveau2 < 0) {
            return false;
        }

        return $niveau1 >= $niveau2;
    }

    /*
    |--------------------------------------------------------------------------
    | GROUPES FONCTIONNELS
    |--------------------------------------------------------------------------
    |
    | Un groupe = un ensemble de rôles ayant accès à un
    | domaine fonctionnel donné.
    |
    | Ces listes sont utilisées par les Policies et les
    | vérifications d'accès.
    */

    /**
     * Rôles ayant accès au back-office en général.
     *
     * @return array<string, string>
     */
    public static function adminRoles(): array
    {
        $tous = self::all();

        unset($tous[self::UTILISATEUR]);

        return $tous;
    }

    /**
     * Rôles ayant accès à la gestion des membres.
     *
     * @return array<int, string>
     */
    public static function gestionMembres(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::SECRETAIRE,
        ];
    }

    /**
     * Rôles ayant accès à la gestion financière complète.
     *
     * @return array<int, string>
     */
    public static function gestionFinances(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::COMPTABLE,
        ];
    }

    /**
     * Rôles ayant accès à la validation des salaires.
     *
     * @return array<int, string>
     */
    public static function validationSalaires(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
        ];
    }

    /**
     * Rôles ayant accès à la gestion des stocks.
     *
     * NB : Le magasinier (si ce rôle est ajouté plus tard)
     * doit être explicitement ajouté ici.
     *
     * @return array<int, string>
     */
    public static function gestionStocks(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::COMPTABLE,
        ];
    }

    /**
     * Rôles ayant accès à la gestion des projets.
     *
     * @return array<int, string>
     */
    public static function gestionProjets(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::CHEF_MISSION,
            self::CHARGE_PROGRAMME,
        ];
    }

    /**
     * Rôles ayant accès à la gestion des dossiers.
     *
     * @return array<int, string>
     */
    public static function gestionDossiers(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::SECRETAIRE,
        ];
    }

    /**
     * Rôles ayant accès à la gestion des utilisateurs.
     *
     * @return array<int, string>
     */
    public static function gestionUtilisateurs(): array
    {
        return [
            self::SUPER_ADMIN,
        ];
    }

    /**
     * Rôles ayant accès à la gestion des paramètres.
     *
     * @return array<int, string>
     */
    public static function gestionParametres(): array
    {
        return [
            self::SUPER_ADMIN,
        ];
    }

    /**
     * Rôles ayant accès à la gestion de la gouvernance.
     *
     * @return array<int, string>
     */
    public static function gestionGouvernance(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
        ];
    }

    /**
     * Rôles ayant accès à la gestion des structures territoriales.
     *
     * @return array<int, string>
     */
    public static function gestionStructures(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::CHEF_MISSION,
            self::CHARGE_PROGRAMME,
        ];
    }

    /**
     * Rôles ayant accès à la rédaction de contenu.
     *
     * @return array<int, string>
     */
    public static function redactionContenu(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::EDITEUR,
        ];
    }

    /**
     * Rôles ayant accès à la messagerie interne.
     *
     * @return array<int, string>
     */
    public static function messagerie(): array
    {
        return [
            self::SUPER_ADMIN,
            self::DG,
            self::CHEF_MISSION,
            self::CHARGE_PROGRAMME,
            self::SECRETAIRE,
        ];
    }
}