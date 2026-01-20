<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// Récupération des catégories pour le sélecteur
$categories = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $id_cat = $_POST['categorie'];
    $prix = $_POST['prix'];
    // AJOUT : On récupère le prix promo, si vide on met NULL
    $prix_promo = !empty($_POST['prix_promo']) ? $_POST['prix_promo'] : NULL;
    $stock = $_POST['stock'];
    $desc = $_POST['description'];

    // Gestion de l'upload d'image
    $chemin_db = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $nom_image = time() . '_' . $_FILES['image']['name'];
        $destination = '../public/Images/' . $nom_image;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $chemin_db = 'public/Images/' . $nom_image;
        }
    }

    if ($nom && $prix && $chemin_db) {
        // REQUÊTE : Ajout du produit dans la base
        $sql = "INSERT INTO produit (nom_produit, id_categorie, prix_unitaire, prix_promo, quantite_stock, description_produit, image_produit) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$nom, $id_cat, $prix, $prix_promo, $stock, $desc, $chemin_db]);
        
        header('Location: produits.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag"> <head>
    <meta charset="UTF-8">
    <title>Ajouter un vêtement - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 800px;">
            <a href="produits.php" class="btn btn-outline-dark rounded-pill">
                <i class="bi bi-arrow-left"></i> Retour au catalogue
            </a>
            <button id="theme-toggle" class="btn btn-outline-dark rounded-circle shadow-sm">
                <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
            </button>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 bg-white p-5 rounded-4 shadow-sm admin-table-card">
                <h2 class="fw-bold mb-4 text-center text-dark-emphasis">Nouveau Produit AfroStyle</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom du produit</label>
                            <input type="text" name="nom" class="form-control rounded-3" placeholder="ex: Boubou Royal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Catégorie</label>
                            <select name="categorie" class="form-select rounded-3">
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_categorie'] ?>"><?= $c['nom_categorie'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prix Normal (FCFA)</label>
                            <input type="number" name="prix" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Prix Promo (Optionnel)</label>
                            <input type="number" name="prix_promo" class="form-control border-danger rounded-3" placeholder="Ex: 5000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Quantité Stock</label>
                            <input type="number" name="stock" class="form-control rounded-3" value="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Détails du tissu, taille disponible..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Image du produit</label>
                        <div class="input-group">
                            <label class="input-group-text bg-dark text-white" for="inputGroupFile01"><i class="bi bi-upload"></i></label>
                            <input type="file" name="image" class="form-control" id="inputGroupFile01" accept="image/*" required>
                        </div>
                        <small class="text-muted">Format recommandé : JPG ou PNG (carré idéalement).</small>
                    </div>

                    <div class="d-flex justify-content-between pt-4 border-top">
                        <a href="produits.php" class="btn btn-light px-4 rounded-pill">Annuler</a>
                        <button type="submit" class="btn btn-dark px-5 rounded-pill shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer le produit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/script.js"></script> </body>
</html>