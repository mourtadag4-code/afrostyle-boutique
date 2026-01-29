

-- 1. CATÉGORIES
INSERT INTO `categorie_produit` (`id_categorie`, `nom_categorie`, `description_categorie_produit`) VALUES
(1, 'Femme', 'Rayonnez dans nos plus belles robes et ensembles. Des tenues colorées et confortables, parfaites pour vos fêtes comme pour tous les jours.'),
(2, 'Homme', 'Soyez élégant en toute simplicité. Nos tenues pour homme allient tradition et modernité pour vous accompagner dans tous vos moments importants.'),
(3, 'Accessoires', 'La touche finale . Découvrez nos chapeaux, sacs et bijoux artisanaux pour rendre votre tenue unique.');

-- 2. FOURNISSEURS
INSERT INTO `fournisseurs` (`id_fournisseur`, `nom`, `prenom`, `telephone`, `adresse`, `email`, `sexe`) VALUES
(1, 'MAMADALY ET FILS', 'Djaouhariat', '+269 331 60 16', 'Anjouan,Mutsamudu,Gongoimwe', 'djaouharia@mamadaly-comores.com', 'M'),
(2, 'GIE Éclat du Teranga', '', '+221 77 123 45 67', 'Village Artisanal de Soumbédioune, Dakar, Sénégal', 'contact@teranga-wax.sn', 'M'),
(3, 'Conakry Indigo Création', 'Fatoumata', '+224 620 00 11 22', 'Marché de Madina, Avenue de la République, Conakry', 'fatou.indigo@guinee-style.gn', 'F'),
(4, 'Lagos Fashion Hub', 'Olusegun', '+234 802 345 6789', 'Lekki Phase 1, Victoria Island, Lagos, Nigeria', 'sales@lagos-hub.ng', 'M');

-- 3. UTILISATEURS
INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `adresse`, `email`, `telephone`, `sexe`, `mot_de_passe`, `role`, `date_inscription`) VALUES
(1, 'Nisrine', 'Attoumane', 'Fann hock_Dakar', 'nisrine.attoumane@afrostyle.com', '+221781060385', 'F', '$2y$10$GmEe54Fi.aNHx3xQACes6OTTPXpTkYJ/f5.FLGe1wMoewBg/Y.jzG', 'administrateur', '2026-01-10 14:31:24'),
(2, 'Soukaina', 'Attoumane', 'Fann hock_Dakar', 'attoumanesoukaina@gmail.com', '+221773974893', 'F', '$2y$10$yoJUD8MJ8iIZEBWcL1LE2.Z31gVnlQy0FjzeMzbNfeLarm4kzHEZS', 'client', '2026-01-13 19:08:52'),
(3, 'SOUNIS', 'Soumaila', 'Fann hock_Dakar', 'sounis@gmail.com', '+221765864559', 'F', '$2y$10$Z3Yp4HSAzV6k0rCufsX85espbMJX5Xwa3hLUIOxHdQAPzPeArWiN2', 'client', '2026-01-13 20:00:23'),
(7, 'Isma', 'Attou', 'Comores', 'ismaattou@gmail.com', '781060385', 'M', '$2y$10$nc0XslezG/7LYdtQOsFva.0Jc6EkOGMZol2vB4/.L0FHKtwH.F/d6', 'client', '2026-01-25 11:43:34'),
(8, 'Admin', 'Test', 'Dakar', 'admin@test.com', '000000', 'M', 'password', 'administrateur', '2026-01-01 00:00:00');

-- 4. PRODUITS (ID 1 À 24)
INSERT INTO `produit` (`id_produit`, `nom_produit`, `prix_unitaire`, `quantite_stock`, `image_produit`, `description_produit`, `origine_modele`, `id_categorie`, `id_fournisseur`, `prix_promo`) VALUES
(1, 'Gaouni', 35000.00, 10, 'public/Images/gaouni.png', 'Magnifique Gaouni traditionnel', 'Comores', 1, 1, NULL),
(2, 'Kandzou', 25000.00, 15, 'public/Images/kandzou.png', 'Élégant Kandzou africain', 'Comores', 2, 1, 15000.00),
(3, 'Kofia', 12000.00, 20, 'public/Images/kofia.png', 'Kofia traditionnelle', 'Comores', 3, 1, 10800.00),
(4, 'Salouva', 30000.00, 12, 'public/Images/salouva.png', 'Salouva colorée', 'Comores', 1, 1, 25000.00),
(5, 'Shiromani', 40000.00, 8, 'public/Images/Shiromani.png', 'Shiromani de luxe pour toute occasion', 'Comores', 1, 1, NULL),
(6, 'Sahari na Soubaya', 25000.00, 7, 'public/Images/sahare.PNG', 'Découvrez l&amp;#039;ensemble Sahari na Soubaya, l&amp;#039;expression ultime du luxe et de la tradition comorienne...', 'Comores', 1, 1, NULL),
(7, 'Dragla', 500000.00, 4, 'public/Images/dragila.PNG', 'Le dragla est un vêtement traditionnel masculin comorien d&#039;apparat...', 'Comores', 2, 1, 400000.00),
(8, 'Nambawane/Lesso', 5000.00, 15, 'public/Images/lesso.png', 'L&#039;indispensable Nambawane : le tissu d&#039;exception des Comores...', 'Comores', 1, 1, NULL),
(9, 'Salouva et kishali', 15000.00, 8, 'public/Images/SALOUVAS.PNG', 'Des jolies salouvas mahoraises', 'Comores', 1, 1, NULL),
(10, 'Djouba', 100000.00, 3, 'public/Images/djouba.PNG', 'Le djouba (ou djuba) est le vêtement traditionnel masculin emblématique...', 'Comores', 2, 1, 80000.00),
(11, 'Chaussures Kabaila', 50000.00, 3, 'public/Images/KABAILA.PNG', '', 'Comores', 3, 1, NULL),
(12, 'Grand boubou', 100000.00, 9, 'public/Images/grand-boubou.PNG', '', 'Sénégal', 2, 2, NULL),
(13, 'Boubou bazin ', 100000.00, 7, 'public/Images/robe-bazin.PNG', 'Robe en bazin riche, confectionnée dans un tissu noble et brillant...', 'Sénégal', 1, 2, NULL),
(14, 'Grand boubou', 100000.00, 7, 'public/Images/Grand-boubou-africain.PNG', 'Grand boubou africain élégant, à la coupe ample et majestueuse...', 'Sénégal', 1, 2, NULL),
(15, 'Robe wax', 50000.00, 19, 'public/Images/robe-wax.PNG', 'Robe en wax élégante, aux motifs africains vibrants...', 'Sénégal', 1, 2, NULL),
(16, 'Ensemble tunique Homme', 150000.00, 10, 'public/Images/Tunique.PNG', 'Ensemble tunique homme élégant, composé d’une tunique raffinée...', 'Nigeria', 2, 1, NULL),
(17, 'Grand boubou ', 25000.00, 1, 'public/Images/trois-pieces.PNG', 'Grand boubou homme trois pièces, comprenant le boubou, la tunique...', 'Guinée', 2, 3, NULL),
(18, 'Boucles d\'oreilles', 5000.00, 8, 'public/Images/boucled\'oreille.PNG', 'Boucles d’oreilles perlées élégantes...', 'Sénégal', 3, 2, NULL),
(19, 'Collier de perles', 10000.00, 10, 'public/Images/Collier.PNG', 'Collier de perles africain, élégant et raffiné...', 'Sénégal', 3, 2, NULL),
(20, 'Chapeau africain (kufi)', 5000.00, 9, 'public/Images/Chapeau.PNG', 'Chapeau africain Kufi, élégant et traditionnel...', 'Nigeria', 3, 4, NULL),
(21, 'Lepi woman', 25000.00, 7, 'public/Images/guinee1.PNG', 'Le précieux tissu indigo tissé par les artisans des hauts plateaux.', 'Guinée', 1, 3, NULL),
(22, 'Habit Traditionnel Nigeria', 100000.00, 24, 'public/Images/nigerienne2.PNG', 'Ensemble apparat richement brodé...', 'Nigeria', 2, 4, NULL),
(23, 'AGBADA', 30000.00, 62, 'public/Images/1769339262_nigerienne1.PNG', 'Le majestueux boubou trois pièces en coton damassé...', 'Nigeria', 2, 4, NULL),
(24, 'Leppi Men', 20000.00, 10, 'public/Images/1769339333_guinee2.PNG', 'élégance du tissage guinéen déclinée...', 'Guinée', 2, 3, NULL);

-- 5. COMMANDES
INSERT INTO `commande` (`id_commande`, `id_utilisateur`, `date_commande`, `montant_total`, `statut_commande`) VALUES
(1, 1, '2026-01-13 18:30:57', 177000.00, 'validee'),
(2, 2, '2026-01-14 10:00:00', 25000.00, 'validee'),
(3, 3, '2026-01-15 12:00:00', 12000.00, 'en_attente'),
(4, 7, '2026-01-20 15:30:00', 50000.00, 'en_attente'),
(5, 1, '2026-01-25 09:00:00', 30000.00, 'validee');

-- 6. DÉTAILS COMMANDES
INSERT INTO `details_commande` (`id_detail`, `id_commande`, `id_produit`, `Quantite_commande`, `prix_unitaire`) VALUES
(1, 1, 4, 2, 30000.00),
(2, 1, 5, 2, 40000.00),
(3, 2, 2, 1, 25000.00),
(4, 3, 3, 1, 12000.00),
(5, 4, 11, 1, 50000.00),
(6, 5, 23, 1, 30000.00);

-- 7. PAIEMENTS (5 ENTRÉES)
INSERT INTO `paiement` (`id_paiement`, `id_commande`, `montant`, `date_paiement`, `methode_paiement`, `statut_paiement`) VALUES
(1, 1, 177000.00, '2026-01-13 18:31:00', 'carte', 'valide'),
(2, 2, 25000.00, '2026-01-14 10:05:00', 'virement', 'valide'),
(3, 3, 12000.00, '2026-01-15 12:10:00', 'carte', 'en_attente'),
(4, 4, 50000.00, '2026-01-20 15:40:00', 'especes', 'en_attente'),
(5, 5, 30000.00, '2026-01-25 09:15:00', 'virement', 'valide');

-- 8. PROMOTIONS
INSERT INTO `promotion` (`id_promotion`, `id_produit`, `code_promo`, `pourcentage_reduction`, `date_debut`, `date_fin`) VALUES
(1, 7, 'PROMO-OR', 10.00, '2026-01-20', '2026-01-21'),
(2, 13, 'BAZIN20', 20.00, '2026-01-22', '2026-01-27'),
(3, 15, 'WAX10', 10.00, '2026-01-22', '2026-02-19'),
(4, 5, 'SHIRO20', 20.00, '2026-01-25', '2026-02-27'),
(5, 3, 'KOFIA10', 10.00, '2026-01-25', '2026-01-30');


-- 9. AVIS 
INSERT INTO `avis` (`id_avis`, `id_utilisateur`, `id_produit`, `note`, `commentaires`, `date_avis`) VALUES
(1, 1, 2, 4, 'C\'est trop joli', '2026-01-14 21:27:48'),
(2, 3, 4, 5, 'Un très bon salouva pour chaque occasion...', '2026-01-14 21:50:47'),
(3, 1, 8, 5, 'Très agréable produit', '2026-01-16 16:30:54'),
(4, 7, 22, 5, 'Qualité incroyable pour le Nigeria', '2026-01-26 10:00:00'),
(5, 2, 13, 4, 'Le bazin brille vraiment bien', '2026-01-27 14:00:00');

-- 10. FAVORIS 
INSERT INTO `favoris` (`id_favoris`, `id_utilisateur`, `id_produit`, `date_ajout`) VALUES
(1, 1, 11, '2026-01-16 14:56:28'),
(2, 1, 7, '2026-01-16 14:56:34'),
(3, 3, 11, '2026-01-16 16:20:47'),
(4, 3, 22, '2026-01-16 16:20:49'),
(5, 7, 24, '2026-01-28 10:00:00'),
(6, 2, 21, '2026-01-28 11:00:00');

-- 11. PANIER 
INSERT INTO `panier` (`id_panier`, `id_utilisateur`, `id_produit`, `quantite`, `date_ajout`) VALUES
(1, 1, 23, 1, '2026-01-20 12:00:00'),
(2, 3, 6, 1, '2026-01-16 16:15:30'),
(3, 3, 8, 1, '2026-01-16 16:15:37'),
(4, 3, 1, 1, '2026-01-16 16:15:54'),
(5, 7, 10, 1, '2026-01-28 15:00:00'),
(6, 2, 15, 2, '2026-01-29 09:00:00');