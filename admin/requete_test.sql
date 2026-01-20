-- Catégorie
INSERT INTO categorie_produit (nom_categorie, description_categorie)
VALUES ('Vêtements traditionnels', 'Vêtements africains traditionnels');

-- Fournisseur
INSERT INTO fournisseurs (nom_societe, telephone, email, adresse)
VALUES ('AfroStyle Supplier', '77889900', 'contact@afrostyle.com', 'Dakar');

-- Produits
INSERT INTO produit (nom_produit, prix_unitaire, quantite_stock, image_produit, description_produit, id_categorie, id_fournisseur)
VALUES
('Gaouni', 35000, 10, 'public/Images/gaouni.png', 'Magnifique Gaouni traditionnel', 1, 1),
('Kandzou', 25000, 15, 'public/Images/kandzou.png', 'Élégant Kandzou africain', 1, 1),
('Kofia', 12000, 20, 'public/Images/kofia.png', 'Kofia traditionnelle', 1, 1),
('Salouva', 30000, 12, 'public/Images/salouva.png', 'Salouva colorée', 1, 1),
('Shiromani', 40000, 8, 'public/Images/Shiromani.png', 'Shiromani de luxe', 1, 1);
