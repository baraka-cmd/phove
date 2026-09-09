<?php

namespace App\Enums;

/**
 * Enum des rôles hiérarchiques PHOVE.
 *
 * Cette classe centralise tous les rôles disponibles dans l'application
 * ainsi que leurs niveaux hiérarchiques et leurs libellés français.
 *
 * Hiérarchie (du plus haut au plus bas) :
 * 5 = Super Admin
 * 4 = Directeur Général
 * 3 = Chef de Mission / Chargé de Programme
 * 2 = Secrétaire Exécutif / Comptable
 * 1 = Caissier / Éditeur
 * 0 = Utilisateur (consultation seule)
 */
final class Roles
{
    /**
     * Rôle Super Admin — Accès total.
     */
    public const SUPER_ADMIN = 'super_admin';

    /**
     * Rôle Directeur Général — Coordonne tout (Article 18).
     */
    public const DG = 'dg';

    /**
     * Rôle Chef de Mission — Coordonne par continent (Article 19).
     */
    public const CHEF_MISSION = 'chef_mission';

    /**
     * Rôle Chargé de Programme — Coordonne par pays (Article 20).
     */
    public const CHARGE_PROGRAMME = 'charge_programme';

    /**
     * Rôle Secrétaire Exécutif — Rédige les rapports (Article 21).
     */
    public const SECRETAIRE = 'secretaire';

    /**
     * Rôle Comptable — Gère les finances (Article 22).
     */
    public const COMPTABLE = 'comptable';

    /**
     * Rôle Caissier — Gère les paiements (Article 24).
     */
    public const CAISSIER = 'caissier';

    /**
     * Rôle Éditeur — Rédige les articles et contenus.
     */
    public const EDITEUR = 'editeur';

    /**
     * Rôle Utilisateur — Consultation seule.
     */
    public const UTILISATEUR = 'utilisateur';

    /**
     * Retourne tous les rôles avec leurs libellés français.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN => 'Super Administrateur',
            self::DG => 'Directeur Général',
            self::CHEF_MISSION => 'Chef de Mission',
            self::CHARGE_PROGRAMME => 'Chargé de Programme',
            self::SECRETAIRE => 'Secrétaire Exécutif',
            self::COMPTABLE => 'Comptable',
            self::CAISSIER => 'Caissier',
            self::EDITEUR => 'Éditeur',
            self::UTILISATEUR => 'Utilisateur',
        ];
    }

    /**
     * Retourne les rôles administratifs (accès au back-office).
     *
     * @return array<string, string>
     */
    public static function adminRoles(): array
    {
        return [
            self::SUPER_ADMIN => 'Super Administrateur',
            self::DG => 'Directeur Général',
            self::CHEF_MISSION => 'Chef de Mission',
            self::CHARGE_PROGRAMME => 'Chargé de Programme',
            self::SECRETAIRE => 'Secrétaire Exécutif',
            self::COMPTABLE => 'Comptable',
            self::CAISSIER => 'Caissier',
            self::EDITEUR => 'Éditeur',
        ];
    }

    /**
     * Retourne les rôles ayant accès à la gestion des membres.
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
     * Retourne les rôles ayant accès à la gestion financière complète.
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
     * Retourne les rôles ayant accès à la validation des salaires.
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
     * Retourne les rôles ayant accès à la gestion des stocks.
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
     * Retourne les rôles ayant accès à la gestion des projets.
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
     * Retourne les rôles ayant accès à la gestion des dossiers.
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
     * Retourne les rôles ayant accès à la gestion des utilisateurs.
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
     * Retourne les rôles ayant accès à la gestion des paramètres.
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
     * Retourne les rôles ayant accès à la gestion de la gouvernance.
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
     * Retourne les rôles ayant accès à la gestion des structures territoriales.
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
     * Retourne les rôles ayant accès à la rédaction de contenu.
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
     * Retourne les rôles ayant accès à la messagerie interne.
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

    /**
     * Vérifie si un rôle existe.
     *
     * @param string $role
     * @return bool
     */
    public static function existe(string $role): bool
    {
        return array_key_exists($role, self::all());
    }

    /**
     * Retourne le libellé français d'un rôle.
     *
     * @param string $role
     * @return string
     */
    public static function libelle(string $role): string
    {
        return self::all()[$role] ?? $role;
    }

    /**
     * Retourne le niveau hiérarchique d'un rôle.
     *
     * @param string $role
     * @return int
     */
    public static function niveau(string $role): int
    {
        return match($role) {
            self::SUPER_ADMIN => 5,
            self::DG => 4,
            self::CHEF_MISSION, self::CHARGE_PROGRAMME => 3,
            self::SECRETAIRE, self::COMPTABLE => 2,
            self::CAISSIER, self::EDITEUR => 1,
            self::UTILISATEUR => 0,
            default => 0,
        };
    }

    /**
     * Vérifie si un rôle a un niveau supérieur ou égal à un autre.
     *
     * @param string $role1
     * @param string $role2
     * @return bool
     */
    public static function superieurOuEgal(string $role1, string $role2): bool
    {
        return self::niveau($role1) >= self::niveau($role2);
    }
}