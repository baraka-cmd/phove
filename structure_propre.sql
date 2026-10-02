
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `extrait` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categorie` enum('actualite','projet','evenement','rapport','temoignage','communique') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actualite',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('brouillon','publie','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_publication` datetime DEFAULT NULL,
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `epingle` tinyint(1) NOT NULL DEFAULT 0,
  `vues` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_user_id_foreign` (`user_id`),
  KEY `articles_projet_id_foreign` (`projet_id`),
  KEY `articles_categorie_index` (`categorie`),
  KEY `articles_statut_index` (`statut`),
  KEY `articles_date_publication_index` (`date_publication`),
  KEY `articles_epingle_index` (`epingle`),
  CONSTRAINT `articles_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assemblees_generales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('ordinaire','extraordinaire') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_reunion` date NOT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre_du_jour` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `membres_total` int(11) NOT NULL DEFAULT 0,
  `membres_presents` int(11) NOT NULL DEFAULT 0,
  `membres_absents` int(11) NOT NULL DEFAULT 0,
  `quorum_atteint` tinyint(1) NOT NULL DEFAULT 0,
  `decisions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolutions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pv_fichier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('planifiee','tenue','annulee','reportee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planifiee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assemblees_generales_date_reunion_index` (`date_reunion`),
  KEY `assemblees_generales_type_index` (`type`),
  KEY `assemblees_generales_statut_index` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beneficiaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projet_id` bigint(20) unsigned NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `categorie` enum('femme','enfant','handicape','troisieme_age','orphelin','deplace_interne','refugie','rapatrie','autre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `localisation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_aide` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_aide` date NOT NULL,
  `description_aide` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temoignage` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temoignage_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beneficiaires_categorie_index` (`categorie`),
  KEY `beneficiaires_localisation_index` (`localisation`),
  KEY `beneficiaires_projet_id_index` (`projet_id`),
  CONSTRAINT `beneficiaires_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bons_sortie` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numéro unique du bon de sortie',
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence documentaire libre du bon',
  `date_demande` date NOT NULL COMMENT 'Date de création de la demande',
  `date_validation` date DEFAULT NULL COMMENT 'Date de validation du bon',
  `date_sortie` date DEFAULT NULL COMMENT 'Date de sortie physique réelle',
  `demandeur_id` bigint(20) unsigned NOT NULL,
  `validateur_id` bigint(20) unsigned DEFAULT NULL,
  `preparateur_id` bigint(20) unsigned DEFAULT NULL,
  `destinataire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bénéficiaire ou destinataire de la sortie',
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `motif` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Motif général de la sortie',
  `statut` enum('brouillon','soumis','valide','en_preparation','sorti','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon' COMMENT 'État du workflow du bon de sortie',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Informations complémentaires',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bons_sortie_numero_unique` (`numero`),
  KEY `bons_sortie_demandeur_id_foreign` (`demandeur_id`),
  KEY `bons_sortie_validateur_id_foreign` (`validateur_id`),
  KEY `bons_sortie_preparateur_id_foreign` (`preparateur_id`),
  KEY `bons_sortie_projet_id_foreign` (`projet_id`),
  KEY `bons_sortie_date_demande_index` (`date_demande`),
  KEY `bons_sortie_date_validation_index` (`date_validation`),
  KEY `bons_sortie_date_sortie_index` (`date_sortie`),
  KEY `bons_sortie_statut_index` (`statut`),
  KEY `bons_sortie_statut_date_index` (`statut`,`date_demande`),
  CONSTRAINT `bons_sortie_demandeur_id_foreign` FOREIGN KEY (`demandeur_id`) REFERENCES `users` (`id`),
  CONSTRAINT `bons_sortie_preparateur_id_foreign` FOREIGN KEY (`preparateur_id`) REFERENCES `users` (`id`),
  CONSTRAINT `bons_sortie_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bons_sortie_validateur_id_foreign` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `motif` enum('partenariat','don','adhesion','benevolat','information','presse','plainte','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'information',
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `date_lecture` datetime DEFAULT NULL,
  `repondu` tinyint(1) NOT NULL DEFAULT 0,
  `date_reponse` datetime DEFAULT NULL,
  `note_interne` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `traite_par` bigint(20) unsigned DEFAULT NULL,
  `statut` enum('nouveau','en_cours','traite','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nouveau',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_traite_par_foreign` (`traite_par`),
  KEY `contacts_lu_index` (`lu`),
  KEY `contacts_statut_index` (`statut`),
  KEY `contacts_motif_index` (`motif`),
  KEY `contacts_created_at_index` (`created_at`),
  CONSTRAINT `contacts_traite_par_foreign` FOREIGN KEY (`traite_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controles_trimestriels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trimestre` enum('T1','T2','T3','T4') COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee` year(4) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `date_controle` date NOT NULL,
  `type_controle` enum('financier','operationnel','global') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global',
  `statut` enum('planifie','en_cours','termine','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planifie',
  `controleur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controle_par` bigint(20) unsigned DEFAULT NULL,
  `total_entrees` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_sorties` decimal(14,2) NOT NULL DEFAULT 0.00,
  `ecart` decimal(14,2) NOT NULL DEFAULT 0.00,
  `conformite` tinyint(1) NOT NULL DEFAULT 1,
  `documents_verifies` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recommandations` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valide_par` bigint(20) unsigned DEFAULT NULL,
  `date_validation` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `controles_trimestriels_trimestre_annee_unique` (`trimestre`,`annee`),
  UNIQUE KEY `controles_trimestriels_reference_unique` (`reference`),
  KEY `controles_trimestriels_controle_par_foreign` (`controle_par`),
  KEY `controles_trimestriels_valide_par_foreign` (`valide_par`),
  KEY `controles_trimestriels_trimestre_index` (`trimestre`),
  KEY `controles_trimestriels_annee_index` (`annee`),
  KEY `controles_trimestriels_statut_index` (`statut`),
  KEY `controles_trimestriels_type_controle_index` (`type_controle`),
  KEY `controles_trimestriels_date_controle_index` (`date_controle`),
  CONSTRAINT `controles_trimestriels_controle_par_foreign` FOREIGN KEY (`controle_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `controles_trimestriels_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cotisations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `membre_id` bigint(20) unsigned NOT NULL,
  `mois` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 50.00,
  `date_paiement` date NOT NULL,
  `mode_paiement` enum('especes','virement_bancaire','mobile_money','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'especes',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enregistre_par` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cotisations_membre_id_mois_unique` (`membre_id`,`mois`),
  KEY `cotisations_enregistre_par_foreign` (`enregistre_par`),
  KEY `cotisations_membre_id_index` (`membre_id`),
  KEY `cotisations_mois_index` (`mois`),
  KEY `cotisations_date_paiement_index` (`date_paiement`),
  CONSTRAINT `cotisations_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cotisations_membre_id_foreign` FOREIGN KEY (`membre_id`) REFERENCES `membres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('statuts','roi','proces_verbal','convention','rapport_annuel','rapport_activite','guide_procedure','politique_interne','communique_officiel','autre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taille` bigint(20) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_document` date DEFAULT NULL,
  `date_signature` date DEFAULT NULL,
  `signataires` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT 0,
  `categorie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telechargements` int(11) NOT NULL DEFAULT 0,
  `upload_par` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_upload_par_foreign` (`upload_par`),
  KEY `documents_type_index` (`type`),
  KEY `documents_public_index` (`public`),
  KEY `documents_date_document_index` (`date_document`),
  CONSTRAINT `documents_upload_par_foreign` FOREIGN KEY (`upload_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents_personnel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dossier_id` bigint(20) unsigned NOT NULL,
  `type_document` enum('demande_motivation','cv','titres_scolaires','attestations_services','attestation_nationalite','attestations_autres','carte_nationalite','carte_hygiene','photos_passeport','carte_vocation','photos_famille','classeur') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type de pièce exigée (Article 65)',
  `fichier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chemin du fichier uploadé',
  `date_upload` date DEFAULT NULL COMMENT 'Date de l''upload du document',
  `upload_par` bigint(20) unsigned DEFAULT NULL,
  `statut` enum('fourni','manquant','en_attente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manquant' COMMENT 'Statut de la pièce',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes sur ce document',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_personnel_dossier_id_type_document_unique` (`dossier_id`,`type_document`),
  KEY `documents_personnel_upload_par_foreign` (`upload_par`),
  KEY `documents_personnel_dossier_id_index` (`dossier_id`),
  KEY `documents_personnel_type_document_index` (`type_document`),
  KEY `documents_personnel_statut_index` (`statut`),
  CONSTRAINT `documents_personnel_dossier_id_foreign` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_personnel` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_personnel_upload_par_foreign` FOREIGN KEY (`upload_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `donateur_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `donateur_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `donateur_telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `donateur_adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_donateur` enum('particulier','organisation','etat','anonyme') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'particulier',
  `montant` decimal(15,2) NOT NULL,
  `devise` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `type_don` enum('ponctuel','recurrent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ponctuel',
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `mode_paiement` enum('especes','virement_bancaire','mobile_money','carte_bancaire','paypal','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'especes',
  `reference_transaction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_don` date NOT NULL,
  `recu_genere` tinyint(1) NOT NULL DEFAULT 0,
  `numero_recu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_recu` date DEFAULT NULL,
  `message_donateur` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('confirme','en_attente','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirme',
  `enregistre_par` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dons_enregistre_par_foreign` (`enregistre_par`),
  KEY `dons_date_don_index` (`date_don`),
  KEY `dons_type_don_index` (`type_don`),
  KEY `dons_statut_index` (`statut`),
  KEY `dons_projet_id_index` (`projet_id`),
  CONSTRAINT `dons_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dons_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossiers_personnel` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `membre_id` bigint(20) unsigned NOT NULL,
  `statut` enum('complet','incomplet','en_attente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente' COMMENT 'État de complétude du dossier',
  `date_creation` date NOT NULL COMMENT 'Date de création du dossier',
  `date_validation` date DEFAULT NULL COMMENT 'Date de validation finale du dossier',
  `valide_par` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes internes sur le dossier',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dossiers_personnel_membre_id_foreign` (`membre_id`),
  KEY `dossiers_personnel_valide_par_foreign` (`valide_par`),
  KEY `dossiers_personnel_statut_index` (`statut`),
  KEY `dossiers_personnel_date_creation_index` (`date_creation`),
  CONSTRAINT `dossiers_personnel_membre_id_foreign` FOREIGN KEY (`membre_id`) REFERENCES `membres` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dossiers_personnel_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emplacements_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Code unique de l’emplacement',
  `nom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nom de l’emplacement',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Description de l’emplacement',
  `actif` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Indique si l’emplacement est actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emplacements_stocks_code_unique` (`code`),
  KEY `emplacements_stocks_actif_index` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indicateurs_projet` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `domaine` enum('sante_nutrition','education','protection','agriculture','infrastructure','economique','spirituel','psychosocial','judiciaire') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Domaine d''intervention PHOVE associé à cet indicateur',
  `nom_indicateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur_cible` int(11) NOT NULL DEFAULT 0,
  `valeur_actuelle` int(11) NOT NULL DEFAULT 0,
  `unite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_mise_a_jour` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicateurs_projet_projet_id_index` (`projet_id`),
  KEY `indicateurs_projet_domaine_index` (`domaine`),
  CONSTRAINT `indicateurs_projet_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaire_stock_lignes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inventaire_stock_id` bigint(20) unsigned NOT NULL,
  `stock_id` bigint(20) unsigned NOT NULL,
  `quantite_theorique` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité système figée au démarrage',
  `quantite_comptee` decimal(15,3) DEFAULT NULL COMMENT 'Quantité physiquement constatée',
  `ecart` decimal(15,3) DEFAULT NULL COMMENT 'Écart entre compté et théorique',
  `unite` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unité de mesure utilisée sur la ligne',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes spécifiques à cette ligne',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventaire_stock_lignes_unique` (`inventaire_stock_id`,`stock_id`),
  KEY `inventaire_stock_lignes_stock_id_foreign` (`stock_id`),
  KEY `inventaire_stock_lignes_ecart_index` (`ecart`),
  CONSTRAINT `inventaire_stock_lignes_inventaire_stock_id_foreign` FOREIGN KEY (`inventaire_stock_id`) REFERENCES `inventaires_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventaire_stock_lignes_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaires_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numéro unique de l’inventaire',
  `emplacement_id` bigint(20) unsigned DEFAULT NULL,
  `date_debut` date NOT NULL COMMENT 'Date de début de l’inventaire',
  `date_cloture` date DEFAULT NULL COMMENT 'Date de clôture du comptage',
  `responsable_id` bigint(20) unsigned NOT NULL,
  `valide_par` bigint(20) unsigned DEFAULT NULL,
  `statut` enum('brouillon','en_cours','termine','valide','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon' COMMENT 'État du workflow de l’inventaire',
  `valide_at` timestamp NULL DEFAULT NULL COMMENT 'Date et heure de validation',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Informations complémentaires',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventaires_stocks_numero_unique` (`numero`),
  KEY `inventaires_stocks_responsable_id_foreign` (`responsable_id`),
  KEY `inventaires_stocks_valide_par_foreign` (`valide_par`),
  KEY `inventaires_stocks_date_debut_index` (`date_debut`),
  KEY `inventaires_stocks_date_cloture_index` (`date_cloture`),
  KEY `inventaires_stocks_statut_index` (`statut`),
  KEY `inventaires_stocks_statut_date_index` (`statut`,`date_debut`),
  KEY `inventaires_stocks_emplacement_date_index` (`emplacement_id`,`date_debut`),
  CONSTRAINT `inventaires_stocks_emplacement_id_foreign` FOREIGN KEY (`emplacement_id`) REFERENCES `emplacements_stocks` (`id`),
  CONSTRAINT `inventaires_stocks_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`),
  CONSTRAINT `inventaires_stocks_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lignes_bons_sortie` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bon_sortie_id` bigint(20) unsigned NOT NULL,
  `stock_id` bigint(20) unsigned NOT NULL,
  `quantite_demandee` decimal(15,3) NOT NULL COMMENT 'Quantité initialement demandée',
  `quantite_validee` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité validée (peut être < demandée)',
  `quantite_sortie` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité réellement sortie du stock',
  `unite` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unité de mesure utilisée sur la ligne',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes spécifiques à cette ligne',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lignes_bons_sortie_bon_stock_unique` (`bon_sortie_id`,`stock_id`),
  KEY `lignes_bons_sortie_stock_bon_index` (`stock_id`,`bon_sortie_id`),
  CONSTRAINT `lignes_bons_sortie_bon_sortie_id_foreign` FOREIGN KEY (`bon_sortie_id`) REFERENCES `bons_sortie` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lignes_bons_sortie_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lignes_reservations_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_stock_id` bigint(20) unsigned NOT NULL,
  `stock_id` bigint(20) unsigned NOT NULL,
  `quantite_demandee` decimal(15,3) NOT NULL COMMENT 'Quantité demandée par l’utilisateur',
  `quantite_reservee` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité réellement bloquée dans le stock',
  `quantite_consommee` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité déjà consommée par les sorties',
  `unite` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unité de mesure utilisée sur la ligne',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes spécifiques à cette ligne',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lignes_reservations_reservation_stock_unique` (`reservation_stock_id`,`stock_id`),
  KEY `lignes_reservations_stocks_stock_id_foreign` (`stock_id`),
  CONSTRAINT `lignes_reservations_stocks_reservation_stock_id_foreign` FOREIGN KEY (`reservation_stock_id`) REFERENCES `reservations_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lignes_reservations_stocks_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lots_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(20) unsigned NOT NULL,
  `numero_lot` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numéro de lot fabricant ou fournisseur',
  `date_fabrication` date DEFAULT NULL COMMENT 'Date de fabrication du produit',
  `date_entree` date NOT NULL COMMENT 'Date d’entrée physique du lot en stock',
  `date_expiration` date DEFAULT NULL COMMENT 'Date d’expiration si produit périssable',
  `quantite_initiale` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité initiale reçue (immuable)',
  `quantite_actuelle` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Quantité restante dans le lot',
  `cout_unitaire` decimal(15,2) DEFAULT NULL COMMENT 'Coût unitaire du produit (nullable pour les dons)',
  `fournisseur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Fournisseur ou organisme donateur',
  `reference_document` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence du document d’entrée du lot',
  `statut` enum('actif','epuise','expire','bloque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif' COMMENT 'État opérationnel du lot',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lots_stocks_stock_numero_unique` (`stock_id`,`numero_lot`),
  KEY `lots_stocks_date_expiration_index` (`date_expiration`),
  KEY `lots_stocks_statut_index` (`statut`),
  KEY `lots_stocks_fefo_index` (`stock_id`,`statut`,`date_expiration`,`date_entree`),
  CONSTRAINT `lots_stocks_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('image','video') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `miniature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taille` bigint(20) unsigned NOT NULL DEFAULT 0,
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `article_id` bigint(20) unsigned DEFAULT NULL,
  `album` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_prise` date DEFAULT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publier` tinyint(1) NOT NULL DEFAULT 1,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medias_user_id_foreign` (`user_id`),
  KEY `medias_type_index` (`type`),
  KEY `medias_album_index` (`album`),
  KEY `medias_projet_id_index` (`projet_id`),
  KEY `medias_publier_index` (`publier`),
  KEY `medias_article_id_foreign` (`article_id`),
  CONSTRAINT `medias_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medias_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membres` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexe` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categorie` enum('initiateur','fondateur','adherent','honneur','sympathisant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_adhesion` date NOT NULL,
  `frais_adhesion` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cotisation_mensuelle` decimal(10,2) NOT NULL DEFAULT 50.00,
  `statut` enum('actif','suspendu','exclu','deserteur','demissionnaire','en_attente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `motif_sanction` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_sanction` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `membres_user_id_foreign` (`user_id`),
  KEY `membres_categorie_index` (`categorie`),
  KEY `membres_statut_index` (`statut`),
  KEY `membres_nom_index` (`nom`),
  CONSTRAINT `membres_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expediteur_id` bigint(20) unsigned NOT NULL,
  `destinataire_id` bigint(20) unsigned NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `date_lecture` datetime DEFAULT NULL,
  `lu_par` bigint(20) unsigned DEFAULT NULL,
  `repondu` tinyint(1) NOT NULL DEFAULT 0,
  `date_reponse` datetime DEFAULT NULL,
  `archived_exp` tinyint(1) NOT NULL DEFAULT 0,
  `archived_dest` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_lu_par_foreign` (`lu_par`),
  KEY `messages_expediteur_id_index` (`expediteur_id`),
  KEY `messages_destinataire_id_index` (`destinataire_id`),
  KEY `messages_lu_index` (`lu`),
  KEY `messages_created_at_index` (`created_at`),
  CONSTRAINT `messages_destinataire_id_foreign` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_expediteur_id_foreign` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_lu_par_foreign` FOREIGN KEY (`lu_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mouvements_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(20) unsigned NOT NULL,
  `lot_stock_id` bigint(20) unsigned DEFAULT NULL,
  `emplacement_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('entree','sortie','transfert','ajustement') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type comptable du mouvement',
  `nature` enum('achat','don','retour','distribution','perte','peremption','inventaire','correction','transfert_interne') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nature métier du mouvement',
  `quantite` decimal(15,3) NOT NULL COMMENT 'Quantité du mouvement (toujours > 0)',
  `sens` smallint(6) NOT NULL DEFAULT 1 COMMENT 'Sens du mouvement : +1, -1 ou 0',
  `date_mouvement` date NOT NULL COMMENT 'Date métier du mouvement',
  `reference_document` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence du document source',
  `bon_sortie_id` bigint(20) unsigned DEFAULT NULL,
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `utilisateur_id` bigint(20) unsigned NOT NULL,
  `motif` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Motif complémentaire du mouvement',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notes complémentaires',
  `statut` enum('valide','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'valide' COMMENT 'État administratif du mouvement',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mouvements_stocks_emplacement_id_foreign` (`emplacement_id`),
  KEY `mouvements_stocks_projet_id_foreign` (`projet_id`),
  KEY `mouvements_stocks_type_index` (`type`),
  KEY `mouvements_stocks_nature_index` (`nature`),
  KEY `mouvements_stocks_date_mouvement_index` (`date_mouvement`),
  KEY `mouvements_stocks_statut_index` (`statut`),
  KEY `mouvements_stocks_stock_date_index` (`stock_id`,`date_mouvement`,`id`),
  KEY `mouvements_stocks_lot_date_index` (`lot_stock_id`,`date_mouvement`,`id`),
  KEY `mouvements_stocks_user_date_index` (`utilisateur_id`,`date_mouvement`),
  KEY `mouvements_stocks_bon_date_index` (`bon_sortie_id`,`date_mouvement`),
  CONSTRAINT `mouvements_stocks_bon_sortie_id_foreign` FOREIGN KEY (`bon_sortie_id`) REFERENCES `bons_sortie` (`id`),
  CONSTRAINT `mouvements_stocks_emplacement_id_foreign` FOREIGN KEY (`emplacement_id`) REFERENCES `emplacements_stocks` (`id`),
  CONSTRAINT `mouvements_stocks_lot_stock_id_foreign` FOREIGN KEY (`lot_stock_id`) REFERENCES `lots_stocks` (`id`),
  CONSTRAINT `mouvements_stocks_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mouvements_stocks_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`),
  CONSTRAINT `mouvements_stocks_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `membre_id` bigint(20) unsigned NOT NULL,
  `structure_id` bigint(20) unsigned DEFAULT NULL,
  `niveau` enum('international','national','provincial','local') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Niveau hiérarchique du poste dans l''organigramme',
  `organe` enum('assemblee_generale','conseil_administration','direction_executive','commission_controle') COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `est_fondateur` tinyint(1) NOT NULL DEFAULT 0,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organes_membre_id_foreign` (`membre_id`),
  KEY `organes_organe_index` (`organe`),
  KEY `organes_actif_index` (`actif`),
  KEY `organes_structure_id_index` (`structure_id`),
  KEY `organes_niveau_index` (`niveau`),
  CONSTRAINT `organes_membre_id_foreign` FOREIGN KEY (`membre_id`) REFERENCES `membres` (`id`) ON DELETE CASCADE,
  CONSTRAINT `organes_structure_id_foreign` FOREIGN KEY (`structure_id`) REFERENCES `structures_territoriales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiements_salaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `salaire_id` bigint(20) unsigned NOT NULL,
  `montant` decimal(12,2) NOT NULL COMMENT 'Montant effectivement versé lors de cette opération.',
  `date_paiement` date NOT NULL COMMENT 'Date effective du paiement.',
  `mode_paiement` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mode utilisé pour effectuer le paiement.',
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Référence unique de l’opération de paiement.',
  `paye_par` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observations ou informations complémentaires sur le paiement.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paiements_salaires_reference_unique` (`reference`),
  KEY `paiements_salaires_salaire_id_index` (`salaire_id`),
  KEY `paiements_salaires_date_paiement_index` (`date_paiement`),
  KEY `paiements_salaires_mode_paiement_index` (`mode_paiement`),
  KEY `paiements_salaires_paye_par_index` (`paye_par`),
  KEY `paiements_salaires_date_mode_index` (`date_paiement`,`mode_paiement`),
  CONSTRAINT `paiements_salaires_paye_par_foreign` FOREIGN KEY (`paye_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `paiements_salaires_salaire_id_foreign` FOREIGN KEY (`salaire_id`) REFERENCES `salaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partenaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('bailleur_fonds','technique','local','international','institutionnel') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partenaires_type_index` (`type`),
  KEY `partenaires_statut_index` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projet_partenaire` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projet_id` bigint(20) unsigned NOT NULL,
  `partenaire_id` bigint(20) unsigned NOT NULL,
  `role` enum('bailleur_principal','co_financeur','partenaire_technique','partenaire_mise_en_oeuvre','partenaire_institutionnel','donateur','sponsor','conseil','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'partenaire_technique',
  `contribution` decimal(15,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projet_partenaire_projet_id_partenaire_id_unique` (`projet_id`,`partenaire_id`),
  KEY `projet_partenaire_projet_id_index` (`projet_id`),
  KEY `projet_partenaire_partenaire_id_index` (`partenaire_id`),
  KEY `projet_partenaire_role_index` (`role`),
  CONSTRAINT `projet_partenaire_partenaire_id_foreign` FOREIGN KEY (`partenaire_id`) REFERENCES `partenaires` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projet_partenaire_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_projet` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Code officiel du projet (ex: UNJP/RDC/082/WFP)',
  `domaine` enum('sante_nutrition','education','protection','wash','securite_alimentaire','communication','environnement','paix','genre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_geographique` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `budget_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `budget_utilise` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bailleur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_financement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('en_preparation','en_cours','termine','suspendu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_preparation',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `objectifs` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resultats_attendus` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_principale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publier` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projets_slug_unique` (`slug`),
  KEY `projets_domaine_index` (`domaine`),
  KEY `projets_statut_index` (`statut`),
  KEY `projets_zone_geographique_index` (`zone_geographique`),
  KEY `projets_date_debut_index` (`date_debut`),
  KEY `projets_code_projet_index` (`code_projet`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rapports_financiers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('mensuel','trimestriel','annuel','projet','audit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `total_entrees` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_sorties` decimal(15,2) NOT NULL DEFAULT 0.00,
  `solde` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dons_recus` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cotisations_recues` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subventions_recues` decimal(15,2) NOT NULL DEFAULT 0.00,
  `autres_entrees` decimal(15,2) NOT NULL DEFAULT 0.00,
  `depenses_projets` decimal(15,2) NOT NULL DEFAULT 0.00,
  `salaires` decimal(15,2) NOT NULL DEFAULT 0.00,
  `logistique` decimal(15,2) NOT NULL DEFAULT 0.00,
  `administratif` decimal(15,2) NOT NULL DEFAULT 0.00,
  `autres_sorties` decimal(15,2) NOT NULL DEFAULT 0.00,
  `fichier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT 0,
  `statut` enum('brouillon','finalise','publie','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `prepare_par` bigint(20) unsigned DEFAULT NULL,
  `date_validation` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rapports_financiers_prepare_par_foreign` (`prepare_par`),
  KEY `rapports_financiers_type_index` (`type`),
  KEY `rapports_financiers_periode_index` (`periode`),
  KEY `rapports_financiers_statut_index` (`statut`),
  KEY `rapports_financiers_public_index` (`public`),
  CONSTRAINT `rapports_financiers_prepare_par_foreign` FOREIGN KEY (`prepare_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numéro unique de la réservation',
  `demandeur_id` bigint(20) unsigned NOT NULL,
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `date_reservation` date NOT NULL COMMENT 'Date de création de la réservation',
  `date_expiration` date DEFAULT NULL COMMENT 'Date limite de validité',
  `statut` enum('brouillon','confirmee','partielle','consommee','expiree','annulee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon' COMMENT 'État du workflow de la réservation',
  `destinataire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bénéficiaire ou destinataire prévu',
  `motif` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Motif de la réservation',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Informations complémentaires',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_stocks_numero_unique` (`numero`),
  KEY `reservations_stocks_demandeur_id_foreign` (`demandeur_id`),
  KEY `reservations_stocks_projet_id_foreign` (`projet_id`),
  KEY `reservations_stocks_date_reservation_index` (`date_reservation`),
  KEY `reservations_stocks_date_expiration_index` (`date_expiration`),
  KEY `reservations_stocks_statut_index` (`statut`),
  KEY `reservations_stocks_actives_index` (`statut`,`date_expiration`),
  CONSTRAINT `reservations_stocks_demandeur_id_foreign` FOREIGN KEY (`demandeur_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reservations_stocks_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `membre_id` bigint(20) unsigned NOT NULL,
  `organe_id` bigint(20) unsigned DEFAULT NULL,
  `mois` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Période salariale au format YYYY-MM.',
  `salaire_base` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Salaire de base du membre pour la période.',
  `primes` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Total des primes et avantages ajoutés au salaire.',
  `retenues` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Total des retenues appliquées au salaire.',
  `salaire_net` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant net dû au membre : salaire_base + primes - retenues.',
  `statut` enum('brouillon','valide','partiellement_paye','paye','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon' COMMENT 'État du salaire dans le cycle de traitement.',
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Référence unique du bulletin salarial.',
  `valide_par` bigint(20) unsigned DEFAULT NULL,
  `valide_at` timestamp NULL DEFAULT NULL COMMENT 'Date et heure exactes de validation du salaire.',
  `annule_par` bigint(20) unsigned DEFAULT NULL,
  `annule_at` timestamp NULL DEFAULT NULL COMMENT 'Date et heure exactes de l’annulation.',
  `motif_annulation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Justification obligatoire lors de l’annulation.',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observations ou informations complémentaires concernant le salaire.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `salaires_membre_mois_unique` (`membre_id`,`mois`),
  UNIQUE KEY `salaires_reference_unique` (`reference`),
  KEY `salaires_annule_par_foreign` (`annule_par`),
  KEY `salaires_mois_statut_index` (`mois`,`statut`),
  KEY `salaires_membre_mois_index` (`membre_id`,`mois`),
  KEY `salaires_organe_mois_index` (`organe_id`,`mois`),
  KEY `salaires_valide_par_index` (`valide_par`),
  KEY `salaires_created_by_index` (`created_by`),
  CONSTRAINT `salaires_annule_par_foreign` FOREIGN KEY (`annule_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salaires_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salaires_membre_id_foreign` FOREIGN KEY (`membre_id`) REFERENCES `membres` (`id`),
  CONSTRAINT `salaires_organe_id_foreign` FOREIGN KEY (`organe_id`) REFERENCES `organes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salaires_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('text','textarea','image','url','email','phone','number','color','switch') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `group` enum('identite','contact','social','pages','seo','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'identite',
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_group_index` (`group`),
  KEY `settings_ordre_index` (`ordre`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Référence métier unique de l’article',
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nom ou désignation de l’article',
  `categorie` enum('vivres','medicaments','habillement','abris','materiel','semences','outils','autre') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Catégorie de l’article',
  `unite` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unité de mesure de l’article',
  `seuil_alerte` decimal(12,2) DEFAULT NULL COMMENT 'Seuil minimum déclenchant une alerte',
  `emplacement_principal_id` bigint(20) unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Description complémentaire de l’article',
  `statut` enum('actif','inactif','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif' COMMENT 'État administratif de l’article',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocks_reference_unique` (`reference`),
  KEY `stocks_categorie_index` (`categorie`),
  KEY `stocks_statut_index` (`statut`),
  KEY `stocks_designation_index` (`designation`),
  KEY `stocks_emplacement_principal_id_index` (`emplacement_principal_id`),
  CONSTRAINT `stocks_emplacement_principal_id_foreign` FOREIGN KEY (`emplacement_principal_id`) REFERENCES `emplacements_stocks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `structures_territoriales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('station','base','antenne') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Station = pays, Base = province, Antenne = territoire/commune',
  `pays` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `territoire` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `statut` enum('actif','inactif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `structures_territoriales_parent_id_foreign` (`parent_id`),
  KEY `structures_territoriales_type_index` (`type`),
  KEY `structures_territoriales_pays_index` (`pays`),
  KEY `structures_territoriales_province_index` (`province`),
  KEY `structures_territoriales_territoire_index` (`territoire`),
  KEY `structures_territoriales_statut_index` (`statut`),
  CONSTRAINT `structures_territoriales_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `structures_territoriales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temoignages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projet_id` bigint(20) unsigned DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_nom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` tinyint(3) unsigned DEFAULT NULL,
  `categorie` enum('femme','enfant','handicape','troisieme_age','orphelin','deplace_interne','refugie','rapatrie','autre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('en_attente','approuve','rejete','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `motif_rejet` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_soumission` datetime DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `traite_par` bigint(20) unsigned DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `temoignages_traite_par_foreign` (`traite_par`),
  KEY `temoignages_statut_index` (`statut`),
  KEY `temoignages_projet_id_index` (`projet_id`),
  KEY `temoignages_categorie_index` (`categorie`),
  KEY `temoignages_province_index` (`province`),
  CONSTRAINT `temoignages_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `temoignages_traite_par_foreign` FOREIGN KEY (`traite_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('super_admin','dg','chef_mission','charge_programme','secretaire','comptable','caissier','editeur','utilisateur') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'utilisateur',
  `statut` enum('actif','inactif','suspendu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `photo_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

