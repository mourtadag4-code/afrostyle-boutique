-- =============================================
-- CRÉATION DES TABLES AFROSTYLE SHOP -
-- =============================================

-- 1. Création de la base de données
DROP DATABASE IF EXISTS `afrostyle_shop`;
CREATE DATABASE IF NOT EXISTS `afrostyle_shop` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `afrostyle_shop`;

-- 2. Table catégorie_produit
CREATE TABLE IF NOT EXISTS `categorie_produit` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(200) NOT NULL,
  `description_categorie_produit` text,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table fournisseurs
CREATE TABLE IF NOT EXISTS `fournisseurs` (
  `id_fournisseur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text,
  `email` varchar(100) DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  PRIMARY KEY (`id_fournisseur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table utilisateurs
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `sexe` enum('M','F') NOT NULL,
  `mot_de_passe` varchar(250) NOT NULL,
  `role` enum('client','administrateur') DEFAULT 'client',
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table produit (avec clés étrangères)
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` int NOT NULL AUTO_INCREMENT,
  `nom_produit` varchar(100) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `quantite_stock` int DEFAULT '0',
  `image_produit` varchar(250) DEFAULT NULL,
  `description_produit` text,
  `origine_modele` varchar(50) DEFAULT 'AfroStyle',
  `id_categorie` int DEFAULT NULL,
  `id_fournisseur` int DEFAULT NULL,
  `prix_promo` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  KEY `fk_produit_categorie` (`id_categorie`),
  KEY `fk_produit_fournisseur` (`id_fournisseur`),
  CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`id_categorie`) 
    REFERENCES `categorie_produit` (`id_categorie`) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_produit_fournisseur` FOREIGN KEY (`id_fournisseur`) 
    REFERENCES `fournisseurs` (`id_fournisseur`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table commande
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `date_commande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `montant_total` decimal(10,2) NOT NULL,
  `statut_commande` enum('en_attente','validee','expediee','livree','annulee') DEFAULT 'en_attente',
  PRIMARY KEY (`id_commande`),
  KEY `fk_commande_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_commande_utilisateur` FOREIGN KEY (`id_utilisateur`) 
    REFERENCES `utilisateurs` (`id_utilisateur`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Table details_commande
CREATE TABLE IF NOT EXISTS `details_commande` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `Quantite_commande` int NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `fk_details_commande` (`id_commande`),
  KEY `fk_details_produit` (`id_produit`),
  CONSTRAINT `fk_details_commande` FOREIGN KEY (`id_commande`) 
    REFERENCES `commande` (`id_commande`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_details_produit` FOREIGN KEY (`id_produit`) 
    REFERENCES `produit` (`id_produit`) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Table avis
CREATE TABLE IF NOT EXISTS `avis` (
  `id_avis` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `note` int DEFAULT NULL,
  `commentaires` text,
  `date_avis` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_avis`),
  KEY `fk_avis_utilisateur` (`id_utilisateur`),
  KEY `fk_avis_produit` (`id_produit`),
  CONSTRAINT `fk_avis_utilisateur` FOREIGN KEY (`id_utilisateur`) 
    REFERENCES `utilisateurs` (`id_utilisateur`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_avis_produit` FOREIGN KEY (`id_produit`) 
    REFERENCES `produit` (`id_produit`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Table favoris
CREATE TABLE IF NOT EXISTS `favoris` (
  `id_favoris` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_favoris`),
  UNIQUE KEY `unique_favoris` (`id_utilisateur`,`id_produit`),
  KEY `fk_favoris_produit` (`id_produit`),
  CONSTRAINT `fk_favoris_utilisateur` FOREIGN KEY (`id_utilisateur`) 
    REFERENCES `utilisateurs` (`id_utilisateur`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favoris_produit` FOREIGN KEY (`id_produit`) 
    REFERENCES `produit` (`id_produit`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Table paiement
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `id_commande` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `methode_paiement` enum('carte','paypal','virement') DEFAULT 'carte',
  `statut_paiement` enum('en_attente','valide','echoue') DEFAULT 'en_attente',
  PRIMARY KEY (`id_paiement`),
  KEY `fk_paiement_commande` (`id_commande`),
  CONSTRAINT `fk_paiement_commande` FOREIGN KEY (`id_commande`) 
    REFERENCES `commande` (`id_commande`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Table panier
CREATE TABLE IF NOT EXISTS `panier` (
  `id_panier` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_panier`),
  KEY `fk_panier_utilisateur` (`id_utilisateur`),
  KEY `fk_panier_produit` (`id_produit`),
  CONSTRAINT `fk_panier_utilisateur` FOREIGN KEY (`id_utilisateur`) 
    REFERENCES `utilisateurs` (`id_utilisateur`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_panier_produit` FOREIGN KEY (`id_produit`) 
    REFERENCES `produit` (`id_produit`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Table promotion
CREATE TABLE IF NOT EXISTS `promotion` (
  `id_promotion` int NOT NULL AUTO_INCREMENT,
  `id_produit` int NOT NULL,
  `code_promo` varchar(50) NOT NULL,
  `pourcentage_reduction` decimal(5,2) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  PRIMARY KEY (`id_promotion`),
  UNIQUE KEY `code_promo` (`code_promo`),
  KEY `fk_promotion_produit` (`id_produit`),
  CONSTRAINT `fk_promotion_produit` FOREIGN KEY (`id_produit`) 
    REFERENCES `produit` (`id_produit`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;