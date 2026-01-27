SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Création de la base
CREATE DATABASE IF NOT EXISTS `afrostyle_shop` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `afrostyle_shop`;
 
-- 1. UTILISATEURS
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(50) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `adresse` TEXT DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  `sexe` ENUM('M','F') NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `role` ENUM('client','administrateur') DEFAULT 'client',
  `date_inscription` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. CATEGORIE_PRODUIT
CREATE TABLE IF NOT EXISTS `categorie_produit` (
  `id_categorie` INT NOT NULL AUTO_INCREMENT,
  `nom_categorie` VARCHAR(200) NOT NULL,
  `description_categorie` TEXT,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- 3. FOURNISSEURS
CREATE TABLE IF NOT EXISTS `fournisseurs` (
  `id_fournisseur` INT NOT NULL AUTO_INCREMENT,
  `nom_societe` VARCHAR(100) NOT NULL,
  `telephone` VARCHAR(20),
  `email` VARCHAR(100),
  `adresse` TEXT,
  PRIMARY KEY (`id_fournisseur`),
  UNIQUE KEY `unique_email_fourn` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. PRODUIT
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` INT NOT NULL AUTO_INCREMENT,
  `nom_produit` VARCHAR(100) NOT NULL,
  `prix_unitaire` DECIMAL(10,2) NOT NULL,
  `prix_promo` DECIMAL(10,2) DEFAULT NULL,
  `quantite_stock` INT DEFAULT '0',
  `image_produit` VARCHAR(250) DEFAULT NULL, -- image principale
  `description_produit` TEXT,
  `origine_modele` VARCHAR(50) DEFAULT 'AfroStyle',
  `id_categorie` INT,
  `id_fournisseur` INT,
  PRIMARY KEY (`id_produit`),
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`id_categorie`) REFERENCES `categorie_produit`(`id_categorie`) ON DELETE SET NULL,
  CONSTRAINT `fk_prod_fourn` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseurs`(`id_fournisseur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 5. COMMANDE
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NOT NULL,
  `date_commande` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `montant_total` DECIMAL(10,2) NOT NULL,
  `statut_commande` ENUM('en_attente','validee','expediee','livree','annulee') DEFAULT 'en_attente',
  PRIMARY KEY (`id_commande`),
  CONSTRAINT `fk_cmd_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs`(`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. DETAILS_COMMANDE
CREATE TABLE IF NOT EXISTS `details_commande` (
  `id_detail` INT NOT NULL AUTO_INCREMENT,
  `id_commande` INT NOT NULL,
  `id_produit` INT NOT NULL,
  `Quantite_commande` INT NOT NULL,
  `prix_unitaire` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  CONSTRAINT `fk_det_cmd` FOREIGN KEY (`id_commande`) REFERENCES `commande`(`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `fk_det_prod` FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. PAIEMENT
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` INT NOT NULL AUTO_INCREMENT,
  `id_commande` INT NOT NULL,
  `montant` DECIMAL(10,2) NOT NULL,
  `date_paiement` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `methode_paiement` ENUM('carte','paypal','virement','mobile_money') DEFAULT 'carte',
  `statut_paiement` ENUM('en_attente','valide','echoue') DEFAULT 'en_attente',
  PRIMARY KEY (`id_paiement`),
  CONSTRAINT `fk_paiement_cmd` FOREIGN KEY (`id_commande`) REFERENCES `commande`(`id_commande`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. PANIER
CREATE TABLE IF NOT EXISTS `panier` (
  `id_panier` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NOT NULL,
  `id_produit` INT NOT NULL,
  `quantite` INT DEFAULT '1',
  PRIMARY KEY (`id_panier`),
  CONSTRAINT `fk_pan_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs`(`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `fk_pan_prod` FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. FAVORIS
CREATE TABLE IF NOT EXISTS `favoris` (
  `id_favoris` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NOT NULL,
  `id_produit` INT NOT NULL,
  `date_ajout` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_favoris`),
  UNIQUE KEY `unique_fav` (`id_utilisateur`,`id_produit`),
  CONSTRAINT `fk_fav_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs`(`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_prod` FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. PROMOTION
CREATE TABLE IF NOT EXISTS `promotion` (
  `id_promotion` INT NOT NULL AUTO_INCREMENT,
  `id_produit` INT NOT NULL,
  `code_promo` VARCHAR(50) NOT NULL,
  `pourcentage_reduction` DECIMAL(5,2) NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  PRIMARY KEY (`id_promotion`),
  UNIQUE KEY `code_promo` (`code_promo`),
  CONSTRAINT `fk_promo_prod` FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. AVIS
CREATE TABLE IF NOT EXISTS `avis` (
  `id_avis` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NOT NULL,
  `id_produit` INT NOT NULL,
  `note` TINYINT NOT NULL, -- 1 à 5, contrôle côté application
  `commentaires` TEXT,
  `date_avis` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_avis`),
  CONSTRAINT `fk_avis_u` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs`(`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `fk_avis_p` FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. IMAGES_PRODUIT (pour plusieurs images par produit)
CREATE TABLE IF NOT EXISTS `images_produit` (
  `id_image` INT NOT NULL AUTO_INCREMENT,
  `id_produit` INT NOT NULL,
  `chemin_image` VARCHAR(250) NOT NULL,
  `titre` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id_image`),
  CONSTRAINT `fk_img_prod` FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;


