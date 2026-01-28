-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 28 jan. 2026 à 21:34
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `afrostyle_shop`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id_avis` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `note` int DEFAULT NULL,
  `commentaires` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_avis` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_avis`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_produit` (`id_produit`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id_avis`, `id_utilisateur`, `id_produit`, `note`, `commentaires`, `date_avis`) VALUES
(1, 1, 2, 4, 'c''est trop joli', '2026-01-14 21:27:48'),
(2, 3, 4, 5, 'Un tres bon salouva pour chaque occasion...Je recommande', '2026-01-14 21:50:47'),
(3, 1, 14, 5, 'tres agreable produit', '2026-01-16 16:30:54'),
(4, 1, 18, 4, 'Un bijou de haut niveau', '2026-01-22 01:49:05'),
(5, 7, 16, 5, 'Un incontournable pour les grands mariages comoriens! So happy pour mon commande.', '2026-01-27 21:12:39');

-- --------------------------------------------------------

--
-- Structure de la table `categorie_produit`
--

DROP TABLE IF EXISTS `categorie_produit`;
CREATE TABLE IF NOT EXISTS `categorie_produit` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_categorie_produit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie_produit`
--

INSERT INTO `categorie_produit` (`id_categorie`, `nom_categorie`, `description_categorie_produit`) VALUES
(1, 'Femme', 'Rayonnez dans nos plus belles robes et ensembles. Des tenues colorées et confortables, parfaites pour vos fêtes comme pour tous les jours.'),
(2, 'Homme', 'Soyez élégant en toute simplicité. Nos tenues pour homme allient tradition et modernité pour vous accompagner dans tous vos moments importants.'),
(3, 'Accessoires', 'La touche finale . Découvrez nos chapeaux, sacs et bijoux artisanaux pour rendre votre tenue unique.');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `date_commande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `montant_total` decimal(10,2) NOT NULL,
  `statut_commande` enum('en_attente','validee','expediee','livree','annulee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  PRIMARY KEY (`id_commande`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `id_utilisateur`, `date_commande`, `montant_total`, `statut_commande`) VALUES
(1, 1, '2026-01-13 18:30:57', 177000.00, 'validee'),
(2, 1, '2026-01-13 18:33:23', 177000.00, 'en_attente'),
(3, 1, '2026-01-13 18:33:47', 177000.00, 'en_attente'),
(4, 1, '2026-01-13 18:36:14', 177000.00, 'en_attente'),
(5, 1, '2026-01-13 18:36:32', 177000.00, 'en_attente'),
(6, 1, '2026-01-13 18:37:13', 177000.00, 'en_attente'),
(7, 1, '2026-01-13 18:37:17', 177000.00, 'en_attente'),
(8, 1, '2026-01-13 18:40:16', 177000.00, 'en_attente'),
(9, 1, '2026-01-13 18:44:14', 42000.00, 'en_attente'),
(10, 2, '2026-01-13 19:36:07', 75000.00, 'en_attente'),
(11, 2, '2026-01-13 19:39:05', 30000.00, 'en_attente'),
(12, 2, '2026-01-13 19:42:46', 60000.00, 'en_attente'),
(13, 2, '2026-01-13 19:45:11', 65000.00, 'en_attente'),
(14, 3, '2026-01-13 20:01:47', 105000.00, 'en_attente'),
(15, 3, '2026-01-13 22:16:37', 315000.00, 'en_attente'),
(16, 1, '2026-01-14 10:41:59', 260000.00, 'en_attente'),
(17, 1, '2026-01-14 16:55:20', 172000.00, 'en_attente'),
(18, 3, '2026-01-14 22:05:59', 202000.00, 'en_attente'),
(19, 3, '2026-01-14 23:08:19', 70000.00, 'en_attente'),
(20, 1, '2026-01-15 12:21:47', 260000.00, 'en_attente'),
(21, 1, '2026-01-15 14:26:10', 3715000.00, 'en_attente'),
(22, 3, '2026-01-15 14:42:55', 15555000.00, 'en_attente'),
(23, 1, '2026-01-16 16:29:57', 12100000.00, 'en_attente'),
(24, 1, '2026-01-17 15:41:08', 12165000.00, 'en_attente'),
(25, 1, '2026-01-19 10:12:41', 12165000.00, 'en_attente'),
(26, 1, '2026-01-19 10:57:19', 4095000.00, 'en_attente'),
(27, 1, '2026-01-19 13:00:45', 3495000.00, 'en_attente'),
(28, 1, '2026-01-19 13:02:20', 3510000.00, 'en_attente'),
(29, 1, '2026-01-19 14:06:38', 9240000.00, 'en_attente'),
(30, 1, '2026-01-19 14:08:34', 95000.00, 'en_attente'),
(31, 1, '2026-01-19 14:09:26', 50000.00, 'en_attente'),
(32, 1, '2026-01-19 14:11:25', 320000.00, 'en_attente'),
(33, 1, '2026-01-19 14:13:14', 3240000.00, 'en_attente'),
(34, 1, '2026-01-19 14:14:06', 3580000.00, 'en_attente'),
(35, 1, '2026-01-19 14:14:47', 80000.00, 'en_attente'),
(36, 1, '2026-01-19 14:17:18', 18000.00, 'en_attente'),
(37, 1, '2026-01-19 14:17:37', 400000.00, 'en_attente'),
(38, 1, '2026-01-19 14:32:49', 280000.00, 'en_attente'),
(39, 1, '2026-01-19 14:35:45', 300000.00, 'en_attente'),
(40, 1, '2026-01-19 14:36:04', 80000.00, 'en_attente'),
(41, 1, '2026-01-19 14:37:54', 160000.00, 'en_attente'),
(42, 1, '2026-01-19 14:40:36', 240000.00, 'en_attente'),
(43, 1, '2026-01-19 14:44:07', 210000.00, 'en_attente'),
(44, 1, '2026-01-19 14:55:38', 135000.00, 'en_attente'),
(45, 1, '2026-01-19 14:55:54', 50000.00, 'en_attente'),
(46, 1, '2026-01-19 15:17:11', 50000.00, 'en_attente'),
(47, 1, '2026-01-19 15:31:09', 12050000.00, 'en_attente'),
(48, 1, '2026-01-19 15:34:38', 3200000.00, 'en_attente'),
(49, 1, '2026-01-19 15:39:22', 1500000.00, 'en_attente'),
(50, 1, '2026-01-19 16:06:32', 3000000.00, 'en_attente'),
(51, 1, '2026-01-19 16:08:15', 330000.00, 'en_attente'),
(52, 1, '2026-01-19 16:10:29', 1565000.00, 'en_attente'),
(53, 3, '2026-01-19 16:11:37', 65000.00, 'en_attente'),
(54, 1, '2026-01-19 16:13:56', 425000.00, 'en_attente'),
(55, 1, '2026-01-19 16:14:18', 1500000.00, 'en_attente'),
(56, 1, '2026-01-19 21:06:42', 430000.00, 'en_attente'),
(57, 1, '2026-01-21 00:35:55', 470000.00, 'en_attente'),
(58, 1, '2026-01-21 12:56:14', 330000.00, 'en_attente'),
(59, 1, '2026-01-22 00:16:33', 342000.00, 'en_attente'),
(60, 1, '2026-01-22 00:22:31', 330000.00, 'en_attente'),
(61, 1, '2026-01-22 01:42:03', 489000.00, 'en_attente'),
(62, 1, '2026-01-22 01:43:52', 330000.00, 'en_attente'),
(63, 1, '2026-01-22 12:12:58', 330000.00, 'en_attente'),
(64, 1, '2026-01-22 16:40:18', 339000.00, 'en_attente'),
(65, 1, '2026-01-23 22:43:18', 469500.00, 'en_attente'),
(66, 1, '2026-01-23 22:49:09', 21600.00, 'en_attente'),
(67, 1, '2026-01-25 10:51:30', 480000.00, 'en_attente'),
(68, 5, '2026-01-25 11:04:09', 45000.00, 'en_attente'),
(69, 7, '2026-01-25 12:14:31', 90000.00, 'en_attente'),
(70, 3, '2026-01-25 12:17:34', 65000.00, 'validee'),
(71, 7, '2026-01-27 21:23:43', 300000.00, 'en_attente'),
(72, 7, '2026-01-27 21:33:49', 5000.00, 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

DROP TABLE IF EXISTS `details_commande`;
CREATE TABLE IF NOT EXISTS `details_commande` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `Quantite_commande` int NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_commande` (`id_commande`),
  KEY `id_produit` (`id_produit`)
) ENGINE=MyISAM AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_detail`, `id_commande`, `id_produit`, `Quantite_commande`, `prix_unitaire`) VALUES
(1, 8, 4, 2, 30000.00),
(2, 8, 5, 2, 40000.00),
(3, 8, 2, 1, 25000.00),
(4, 8, 3, 1, 12000.00),
(5, 9, 4, 1, 30000.00),
(6, 9, 3, 1, 12000.00),
(7, 10, 5, 1, 40000.00),
(8, 10, 1, 1, 35000.00),
(9, 11, 4, 1, 30000.00),
(10, 12, 1, 1, 35000.00),
(11, 12, 2, 1, 25000.00),
(12, 13, 4, 1, 30000.00),
(13, 13, 1, 1, 35000.00),
(14, 14, 5, 1, 40000.00),
(15, 14, 4, 1, 30000.00),
(16, 14, 1, 1, 35000.00),
(17, 15, 5, 3, 40000.00),
(18, 15, 4, 3, 30000.00),
(19, 15, 1, 3, 35000.00),
(20, 16, 5, 2, 40000.00),
(21, 16, 4, 4, 30000.00),
(22, 16, 3, 5, 12000.00),
(23, 17, 4, 4, 30000.00),
(24, 17, 3, 1, 12000.00),
(25, 17, 5, 1, 40000.00),
(26, 18, 5, 1, 40000.00),
(27, 18, 4, 5, 30000.00),
(28, 18, 3, 1, 12000.00),
(29, 19, 5, 1, 40000.00),
(30, 19, 4, 1, 30000.00),
(31, 20, 11, 1, 100000.00),
(32, 20, 5, 1, 40000.00),
(33, 20, 4, 4, 30000.00),
(34, 21, 11, 1, 100000.00),
(35, 21, 18, 1, 3000000.00),
(36, 21, 17, 1, 50000.00),
(37, 21, 13, 1, 500000.00),
(38, 21, 5, 1, 40000.00),
(39, 21, 12, 1, 25000.00),
(40, 22, 18, 5, 3000000.00),
(41, 22, 17, 1, 50000.00),
(42, 22, 13, 1, 500000.00),
(43, 22, 14, 1, 5000.00),
(44, 23, 18, 4, 3000000.00),
(45, 23, 17, 2, 50000.00),
(46, 24, 18, 4, 3000000.00),
(47, 24, 17, 3, 50000.00),
(48, 24, 14, 3, 5000.00),
(49, 25, 18, 4, 3000000.00),
(50, 25, 17, 3, 50000.00),
(51, 25, 14, 3, 5000.00),
(52, 26, 15, 3, 15000.00),
(53, 26, 18, 4, 1000000.00),
(54, 26, 17, 1, 50000.00),
(55, 27, 17, 3, 50000.00),
(56, 27, 16, 2, 100000.00),
(57, 27, 5, 3, 40000.00),
(58, 28, 17, 3, 50000.00),
(59, 28, 16, 2, 100000.00),
(60, 28, 5, 3, 40000.00),
(61, 28, 15, 1, 15000.00),
(62, 28, 12, 1, 25000.00),
(63, 28, 18, 1, 3000000.00),
(64, 29, 18, 3, 3000000.00),
(65, 29, 16, 3, 80000.00),
(66, 30, 14, 3, 5000.00),
(67, 30, 16, 1, 80000.00),
(68, 31, 17, 1, 50000.00),
(69, 32, 16, 4, 80000.00),
(70, 33, 18, 1, 3000000.00),
(71, 33, 16, 3, 80000.00),
(72, 34, 18, 1, 3000000.00),
(73, 34, 16, 1, 80000.00),
(74, 34, 13, 1, 500000.00),
(75, 35, 16, 1, 80000.00),
(76, 36, 4, 1, 15000.00),
(77, 37, 16, 5, 80000.00),
(78, 38, 16, 1, 80000.00),
(79, 38, 17, 4, 50000.00),
(80, 39, 16, 3, 100000.00),
(81, 40, 16, 1, 80000.00),
(82, 41, 16, 2, 80000.00),
(83, 42, 16, 3, 80000.00),
(84, 43, 16, 2, 80000.00),
(85, 43, 17, 1, 50000.00),
(86, 44, 16, 1, 80000.00),
(87, 44, 17, 1, 50000.00),
(88, 44, 14, 1, 5000.00),
(89, 45, 17, 1, 50000.00),
(90, 46, 17, 1, 50000.00),
(91, 47, 18, 4, 3000000.00),
(92, 47, 17, 1, 50000.00),
(93, 48, 18, 2, 1500000.00),
(94, 48, 17, 1, 25000.00),
(95, 48, 16, 2, 80000.00),
(96, 48, 14, 3, 5000.00),
(97, 49, 18, 1, 1500000.00),
(98, 50, 18, 2, 1500000.00),
(99, 51, 17, 1, 50000.00),
(100, 51, 14, 3, 5000.00),
(101, 51, 15, 3, 15000.00),
(102, 51, 16, 1, 100000.00),
(103, 51, 5, 3, 40000.00),
(104, 52, 18, 1, 1500000.00),
(105, 52, 17, 1, 25000.00),
(106, 52, 5, 1, 40000.00),
(107, 53, 12, 1, 25000.00),
(108, 53, 14, 1, 5000.00),
(109, 53, 1, 1, 35000.00),
(110, 54, 17, 2, 50000.00),
(111, 54, 14, 4, 5000.00),
(112, 54, 15, 3, 15000.00),
(113, 54, 16, 1, 100000.00),
(114, 54, 5, 4, 40000.00),
(115, 55, 18, 1, 1500000.00),
(116, 56, 17, 3, 50000.00),
(117, 56, 14, 3, 5000.00),
(118, 56, 15, 3, 15000.00),
(119, 56, 16, 1, 100000.00),
(120, 56, 5, 3, 40000.00),
(121, 57, 17, 3, 50000.00),
(122, 57, 14, 3, 5000.00),
(123, 57, 15, 3, 15000.00),
(124, 57, 16, 1, 100000.00),
(125, 57, 5, 4, 40000.00),
(126, 58, 17, 1, 50000.00),
(127, 58, 14, 3, 5000.00),
(128, 58, 15, 3, 15000.00),
(129, 58, 16, 1, 100000.00),
(130, 58, 5, 3, 40000.00),
(131, 59, 17, 1, 50000.00),
(132, 59, 14, 3, 5000.00),
(133, 59, 15, 3, 15000.00),
(134, 59, 16, 1, 100000.00),
(135, 59, 5, 3, 40000.00),
(136, 59, 3, 1, 12000.00),
(137, 60, 17, 1, 50000.00),
(138, 60, 14, 3, 5000.00),
(139, 60, 15, 3, 15000.00),
(140, 60, 16, 1, 100000.00),
(141, 60, 5, 3, 40000.00),
(142, 61, 17, 3, 50000.00),
(143, 61, 14, 4, 5000.00),
(144, 61, 15, 3, 15000.00),
(145, 61, 16, 1, 100000.00),
(146, 61, 5, 3, 40000.00),
(147, 61, 28, 2, 27000.00),
(148, 62, 17, 1, 50000.00),
(149, 62, 14, 3, 5000.00),
(150, 62, 15, 3, 15000.00),
(151, 62, 16, 1, 100000.00),
(152, 62, 5, 3, 40000.00),
(153, 63, 17, 1, 50000.00),
(154, 63, 14, 3, 5000.00),
(155, 63, 15, 3, 15000.00),
(156, 63, 16, 1, 100000.00),
(157, 63, 5, 3, 40000.00),
(158, 64, 17, 1, 50000.00),
(159, 64, 14, 3, 5000.00),
(160, 64, 15, 3, 15000.00),
(161, 64, 16, 1, 100000.00),
(162, 64, 5, 3, 40000.00),
(163, 64, 38, 2, 4500.00),
(164, 65, 17, 2, 50000.00),
(165, 65, 15, 3, 15000.00),
(166, 65, 16, 2, 100000.00),
(167, 65, 5, 3, 40000.00),
(168, 65, 38, 1, 4500.00),
(169, 66, 3, 2, 10800.00),
(170, 67, 17, 4, 50000.00),
(171, 67, 14, 3, 5000.00),
(172, 67, 15, 3, 15000.00),
(173, 67, 16, 1, 100000.00),
(174, 67, 5, 3, 40000.00),
(175, 68, 33, 1, 45000.00),
(176, 69, 41, 3, 30000.00),
(177, 70, 12, 1, 25000.00),
(178, 70, 14, 1, 5000.00),
(179, 70, 1, 1, 35000.00),
(180, 71, 40, 3, 100000.00),
(181, 72, 38, 1, 5000.00);

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
CREATE TABLE IF NOT EXISTS `favoris` (
  `id_favoris` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_favoris`),
  UNIQUE KEY `unique_favoris` (`id_utilisateur`,`id_produit`),
  KEY `id_produit` (`id_produit`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id_favoris`, `id_utilisateur`, `id_produit`, `date_ajout`) VALUES
(1, 1, 17, '2026-01-16 14:56:28'),
(12, 1, 14, '2026-01-22 00:12:57'),
(3, 1, 13, '2026-01-16 14:56:34'),
(4, 3, 17, '2026-01-16 16:20:47'),
(5, 3, 18, '2026-01-16 16:20:49'),
(6, 3, 3, '2026-01-16 16:20:52'),
(7, 1, 18, '2026-01-19 10:05:31'),
(8, 1, 15, '2026-01-19 10:12:58'),
(9, 1, 3, '2026-01-19 11:42:32'),
(10, 1, 1, '2026-01-19 12:02:28'),
(11, 1, 4, '2026-01-19 12:11:59'),
(13, 1, 23, '2026-01-22 00:13:01'),
(14, 1, 28, '2026-01-22 00:59:15'),
(15, 1, 29, '2026-01-22 01:19:35'),
(16, 1, 38, '2026-01-22 11:59:17'),
(17, 1, 35, '2026-01-22 12:02:58'),
(18, 1, 37, '2026-01-22 12:11:19'),
(19, 1, 36, '2026-01-22 12:11:21'),
(20, 1, 33, '2026-01-22 12:11:41'),
(21, 5, 33, '2026-01-25 11:03:57'),
(22, 7, 41, '2026-01-27 21:23:29');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

DROP TABLE IF EXISTS `fournisseurs`;
CREATE TABLE IF NOT EXISTS `fournisseurs` (
  `id_fournisseur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_fournisseur`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id_fournisseur`, `nom`, `prenom`, `telephone`, `adresse`, `email`, `sexe`) VALUES
(1, 'MAMADALY ET FILS', 'Djaouhariat', '+269 331 60 16', 'Anjouan,Mutsamudu,Gongoimwe', 'djaouharia@mamadaly-comores.com', 'M'),
(2, 'GIE Éclat du Teranga', '', '+221 77 123 45 67', 'Village Artisanal de Soumbédioune, Dakar, Sénégal', 'contact@teranga-wax.sn', 'M'),
(3, 'Conakry Indigo Création', 'Fatoumata', '+224 620 00 11 22', 'Marché de Madina, Avenue de la République, Conakry', 'fatou.indigo@guinee-style.gn', 'F'),
(4, 'Lagos Fashion Hub', 'Olusegun', '+234 802 345 6789', 'Lekki Phase 1, Victoria Island, Lagos, Nigeria', 'sales@lagos-hub.ng', 'M');

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `id_commande` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `methode_paiement` enum('carte','paypal','virement') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'carte',
  `statut_paiement` enum('en_attente','valide','echoue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  PRIMARY KEY (`id_paiement`),
  KEY `id_commande` (`id_commande`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiement`
--

INSERT INTO `paiement` (`id_paiement`, `id_commande`, `montant`, `date_paiement`, `methode_paiement`, `statut_paiement`) VALUES
(1, 29, 9240000.00, '2026-01-19 14:06:38', '', 'en_attente'),
(2, 30, 95000.00, '2026-01-19 14:08:34', '', 'en_attente'),
(3, 31, 50000.00, '2026-01-19 14:09:26', '', 'en_attente'),
(4, 32, 320000.00, '2026-01-19 14:11:25', '', 'en_attente'),
(5, 33, 3240000.00, '2026-01-19 14:13:14', 'carte', 'en_attente'),
(6, 34, 3580000.00, '2026-01-19 14:14:06', '', 'en_attente'),
(7, 35, 80000.00, '2026-01-19 14:14:47', 'virement', 'en_attente'),
(8, 36, 18000.00, '2026-01-19 14:17:18', '', 'en_attente'),
(9, 37, 400000.00, '2026-01-19 14:17:37', 'carte', 'en_attente'),
(10, 38, 280000.00, '2026-01-19 14:32:49', 'carte', 'en_attente'),
(11, 39, 300000.00, '2026-01-19 14:35:45', '', 'en_attente'),
(12, 40, 80000.00, '2026-01-19 14:36:04', '', 'en_attente'),
(13, 41, 160000.00, '2026-01-19 14:37:54', '', 'en_attente'),
(14, 42, 240000.00, '2026-01-19 14:40:36', 'carte', 'en_attente'),
(15, 43, 210000.00, '2026-01-19 14:44:07', '', 'en_attente'),
(16, 44, 135000.00, '2026-01-19 14:55:38', 'carte', 'en_attente'),
(17, 45, 50000.00, '2026-01-19 14:55:54', '', 'en_attente'),
(18, 46, 50000.00, '2026-01-19 15:17:11', '', 'en_attente'),
(19, 47, 12050000.00, '2026-01-19 15:31:09', '', 'en_attente'),
(20, 48, 3200000.00, '2026-01-19 15:34:38', '', 'en_attente'),
(21, 49, 1500000.00, '2026-01-19 15:39:22', '', 'en_attente'),
(22, 52, 1565000.00, '2026-01-19 16:10:29', '', 'en_attente'),
(23, 53, 65000.00, '2026-01-19 16:11:37', 'carte', 'en_attente'),
(24, 54, 425000.00, '2026-01-19 16:13:56', '', 'en_attente'),
(25, 55, 1500000.00, '2026-01-19 16:14:18', '', 'en_attente'),
(26, 56, 430000.00, '2026-01-19 21:06:42', 'carte', 'en_attente'),
(27, 57, 470000.00, '2026-01-21 00:35:55', 'carte', 'en_attente'),
(28, 58, 330000.00, '2026-01-21 12:56:14', '', 'en_attente'),
(29, 59, 342000.00, '2026-01-22 00:16:33', '', 'en_attente'),
(30, 60, 330000.00, '2026-01-22 00:22:31', 'carte', 'en_attente'),
(31, 63, 330000.00, '2026-01-22 12:12:58', 'carte', 'en_attente'),
(32, 64, 339000.00, '2026-01-22 16:40:18', '', 'en_attente'),
(33, 65, 469500.00, '2026-01-23 22:43:18', 'carte', 'en_attente'),
(34, 66, 21600.00, '2026-01-23 22:49:09', 'carte', 'en_attente'),
(35, 67, 480000.00, '2026-01-25 10:51:30', 'carte', 'en_attente'),
(36, 68, 45000.00, '2026-01-25 11:04:09', '', 'en_attente'),
(37, 69, 90000.00, '2026-01-25 12:14:31', 'carte', 'en_attente'),
(38, 71, 300000.00, '2026-01-27 21:23:43', '', 'en_attente'),
(39, 72, 5000.00, '2026-01-27 21:33:49', 'carte', 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `id_panier` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_panier`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_produit` (`id_produit`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id_panier`, `id_utilisateur`, `id_produit`, `quantite`, `date_ajout`) VALUES
(2, 1, 17, 1, '2026-01-16 14:56:23'),
(3, 3, 12, 1, '2026-01-16 16:15:30'),
(4, 3, 14, 1, '2026-01-16 16:15:37'),
(5, 3, 1, 1, '2026-01-16 16:15:54'),
(6, 1, 14, 3, '2026-01-16 16:31:26'),
(7, 1, 15, 3, '2026-01-19 10:12:54'),
(8, 1, 16, 1, '2026-01-19 10:57:33'),
(9, 1, 5, 3, '2026-01-19 11:18:47');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` int NOT NULL AUTO_INCREMENT,
  `nom_produit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `quantite_stock` int DEFAULT '0',
  `image_produit` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_produit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `origine_modele` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'AfroStyle',
  `id_categorie` int DEFAULT NULL,
  `id_fournisseur` int DEFAULT NULL,
  `prix_promo` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  KEY `id_categorie` (`id_categorie`),
  KEY `id_fournisseur` (`id_fournisseur`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `nom_produit`, `prix_unitaire`, `quantite_stock`, `image_produit`, `description_produit`, `origine_modele`, `id_categorie`, `id_fournisseur`, `prix_promo`) VALUES
(1, 'Gaouni', 35000.00, 10, 'public/Images/gaouni.png', 'Magnifique Gaouni traditionnel', 'Comores', 1, 1, NULL),
(2, 'Kandzou', 25000.00, 15, 'public/Images/kandzou.png', 'Élégant Kandzou africain', 'Comores', 2, 1, 15000.00),
(3, 'Kofia', 12000.00, 20, 'public/Images/kofia.png', 'Kofia traditionnelle', 'Comores', 3, 1, 10800.00),
(4, 'Salouva', 30000.00, 12, 'public/Images/salouva.png', 'Salouva colorée', 'Comores', 1, 1, 25000.00),
(5, 'Shiromani', 40000.00, 8, 'public/Images/Shiromani.png', 'Shiromani de luxe pour toute occasion', 'Comores', 1, 1, NULL),
(14, 'Nambawane/Lesso', 5000.00, 15, 'public/Images/lesso.png', 'L\'indispensable Nambawane : le tissu d\'exception des Comores. Alliez la douceur d\'un coton premium à l\'élégance des motifs traditionnels. Avec ses proverbes uniques et ses couleurs vibrantes, c\'est le choix n°1 pour des salouvas raffinés qui affirment votre identité avec éclat.\r\n\r\n', 'Comores', 1, 1, NULL),
(12, 'Sahari na Soubaya', 25000.00, 7, 'public/Images/sahare.PNG', 'Découvrez l\'ensemble Sahari na Soubaya, l\'expression ultime du luxe et de la tradition comorienne. Originaire de l\'île de Ngazidja, cette tenue d\'exception est le pilier vestimentaire du Anda (le Grand Mariage), symbolisant la dignité et le nouveau statut social de la femme marié', 'Comores', 1, 1, NULL),
(13, 'Dragla', 500000.00, 4, 'public/Images/dragila.PNG', 'Le dragla est un vêtement traditionnel masculin comorien d\'apparat, caractérisé par des tissus légers et de riches broderies au fil d\'or (ou fil doré "ouzi"). Porté lors d\'occasions spéciales, notamment le grand mariage traditionnel (Anda), il symbolise le prestige et est souvent cousu et décoré par des femmes. ', 'Comores', 2, 1, 400000.00),
(15, 'Salouva et kishali', 15000.00, 8, 'public/Images/SALOUVAS.PNG', 'Des jolies salouvas mahoraises', 'Comores', 1, 1, NULL),
(16, 'Djouba', 100000.00, 3, 'public/Images/djouba.PNG', 'Le djouba (ou djuba) est le vêtement traditionnel masculin emblématique des Comores, porté principalement lors de grandes occasions comme le Grand Mariage ou les fêtes religieuses .\r\n', 'Comores', 2, 1, 80000.00),
(17, 'Chaussures Kabaila', 50000.00, 3, 'public/Images/KABAILA.PNG', '', 'Comores', 3, 1, NULL),
(31, 'Boubou bazin ', 100000.00, 7, 'public/Images/robe-bazin.PNG', 'Robe en bazin riche, confectionnée dans un tissu noble et brillant, symbole d’élégance africaine. Sa coupe raffinée met en valeur la silhouette, tandis que la qualité du bazin assure confort, tenue et durabilité. Idéale pour les cérémonies, fêtes et grandes occasions, cette robe incarne le prestige et le savoir-faire traditionnel.', 'Sénégal', 1, 2, NULL),
(30, 'Grand boubou', 100000.00, 9, 'public/Images/grand-boubou.PNG', '', 'Sénégal', 2, 2, NULL),
(32, 'Grand boubou', 100000.00, 7, 'public/Images/Grand-boubou-africain.PNG', 'Grand boubou africain élégant, à la coupe ample et majestueuse, confectionné dans un tissu de qualité offrant confort et distinction. Idéal pour les cérémonies et grandes occasions.', 'Sénégal', 1, 2, NULL),
(33, 'Robe wax', 50000.00, 19, 'public/Images/robe-wax.PNG', 'Robe en wax élégante, aux motifs africains vibrants, confectionnée dans un tissu de qualité offrant confort et style. Parfaite pour les sorties, cérémonies et occasions spéciales.', 'Sénégal', 1, 2, NULL),
(34, 'Ensemble tunique Homme', 150000.00, 10, 'public/Images/Tunique.PNG', 'Ensemble tunique homme élégant, composé d’une tunique raffinée et d’un pantalon assorti, offrant confort et style. Idéal pour les occasions formelles comme décontractées.', 'Nigeria', 2, 1, NULL),
(35, 'Grand boubou ', 25000.00, 1, 'public/Images/trois-pieces.PNG', 'Grand boubou homme trois pièces, comprenant le boubou, la tunique, le pantalon et le chapeau assorti, alliant confort, élégance et tradition. Idéal pour les cérémonies et grandes occasions.', 'Guinée', 2, 3, NULL),
(36, 'Boucles d\'oreilles', 5000.00, 8, 'public/Images/boucled\'oreille.PNG', 'Boucles d’oreilles perlées élégantes, réalisées avec des perles fines et colorées, apportant une touche de style et de raffinement à toutes vos tenues. Parfaites pour les occasions spéciales ou le quotidien.', 'Sénégal', 3, 2, NULL),
(37, 'Collier de perles', 10000.00, 10, 'public/Images/Collier.PNG', 'Collier de perles africain, élégant et raffiné, confectionné à la main avec des perles colorées de qualité, idéal pour sublimer vos tenues traditionnelles ou modernes.', 'Sénégal', 3, 2, NULL),
(38, 'Chapeau africain (kufi)', 5000.00, 9, 'public/Images/Chapeau.PNG', 'Chapeau africain Kufi, élégant et traditionnel, confectionné dans un tissu de qualité pour confort et style. Parfait pour compléter vos tenues africaines ou cérémoniales.', 'Nigeria', 3, 4, NULL),
(39, 'Lepi woman', 25000.00, 7, 'public/Images/guinee1.PNG', 'Le précieux tissu indigo tissé par les artisans des hauts plateaux.', 'Guinée', 1, 3, NULL),
(40, 'Habit Traditionnel Nigeria', 100000.00, 24, 'public/Images/nigerienne2.PNG', 'Ensemble d\'apparat richement brodé pour une célébration inoubliable.', 'Nigeria', 2, 4, NULL),
(41, 'AGBADA', 30000.00, 62, 'public/Images/1769339262_nigerienne1.PNG', 'Le majestueux boubou trois pièces en coton damassé de haute qualité.', 'Nigeria', 2, 4, NULL),
(42, 'Leppi Men', 20000.00, 10, 'public/Images/1769339333_guinee2.PNG', 'L\'élégance du tissage guinéen déclinée pour l\'homme moderne', 'Guinée', 2, 3, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `id_promotion` int NOT NULL AUTO_INCREMENT,
  `id_produit` int NOT NULL,
  `code_promo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pourcentage_reduction` decimal(5,2) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  PRIMARY KEY (`id_promotion`),
  UNIQUE KEY `code_promo` (`code_promo`),
  KEY `id_produit` (`id_produit`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `promotion`
--

INSERT INTO `promotion` (`id_promotion`, `id_produit`, `code_promo`, `pourcentage_reduction`, `date_debut`, `date_fin`) VALUES
(15, 3, 'SN17', 10.00, '2026-01-25', '2026-01-30'),
(16, 13, 'Djanis17', 20.00, '2026-01-25', '2026-01-30'),
(11, 38, 'nim17', 10.00, '2026-01-22', '2026-01-23'),
(5, 13, 'sounis17', 10.00, '2026-01-20', '2026-01-21'),
(14, 5, 'MN17', 20.00, '2026-01-25', '2026-02-27'),
(9, 31, 'nis123', 20.00, '2026-01-22', '2026-01-27'),
(10, 33, 'nis1017', 10.00, '2026-01-22', '2026-02-19');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','administrateur') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'client',
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `adresse`, `email`, `telephone`, `sexe`, `mot_de_passe`, `role`, `date_inscription`, `reset_token`, `reset_expires_at`) VALUES
(1, 'Nisrine', 'Attoumane', 'Fann hock_Dakar', 'nisrine.attoumane@afrostyle.com', '+221781060385', 'F', '$2y$10$GmEe54Fi.aNHx3xQACes6OTTPXpTkYJ/f5.FLGe1wMoewBg/Y.jzG', 'administrateur', '2026-01-10 14:31:24', 'a4fbc96991ec1cf411489edc6d2438e52d8138022cce94f44cfa5ce7eb1223cb', '2026-01-25 12:02:31'),
(2, 'Soukaina', 'Attoumane', 'Fann hock_Dakar', 'attoumanesoukaina@gmail.com', '+221773974893', 'F', '$2y$10$yoJUD8MJ8iIZEBWcL1LE2.Z31gVnlQy0FjzeMzbNfeLarm4kzHEZS', 'client', '2026-01-13 19:08:52', NULL, NULL),
(3, 'SOUNIS', 'Soumaila', 'Fann hock_Dakar', 'sounis@gmail.com', '+221765864559', 'F', '$2y$10$Z3Yp4HSAzV6k0rCufsX85espbMJX5Xwa3hLUIOxHdQAPzPeArWiN2', 'client', '2026-01-13 20:00:23', NULL, NULL),
(4, 'Nesse', 'Attoumane', 'Fann hock_Dakar', 'attoumanenisrine10@gmail.com', '+221781060385', 'F', '$2y$10$rm.m.nF2uzU3.MUtC53Zo.DlrEAn/d0dUfVe.x.jz36LYxmGFUmSu', 'client', '2026-01-21 12:30:25', 'fd15b28e63bcd8626c29c0eab8b32313d75f8372c7529ec47f6af3ff356149a4', '2026-01-21 13:33:12'),
(5, 'Nisrine', 'Attoumane', 'Fann hock_Dakar', 'attoumanenisrine1@gmail.com', '+221781060385', 'F', '$2y$10$RUPpQwwOWKZ7Cc5ijfjImuyADPOtuqlr2xOlbgDgkFAERuFgKLMxm', 'client', '2026-01-25 11:02:56', NULL, NULL),
(6, 'Djaouhariat', 'Saindou', 'Anjouan, Comores', 'djaousaindou@gmail.com', '+221 331 60 16', 'F', '$2y$10$ULPOaZaXkD6BTiwDWV1o5udadmV93eHMp9Dy/LrBigdVTF6kRUR8W', 'client', '2026-01-25 11:33:14', NULL, NULL),
(7, 'Isma', 'Attou', 'Comores', 'ismaattou@gmail.com', '781060385', 'M', '$2y$10$nc0XslezG/7LYdtQOsFva.0Jc6EkOGMZol2vB4/.L0FHKtwH.F/d6', 'client', '2026-01-25 11:43:34', NULL, NULL);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;