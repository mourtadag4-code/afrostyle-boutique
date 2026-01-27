<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// 1. RÉCUPÉRATION DES CATÉGORIES ET DES FOURNISSEURS
$categories = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();
// On récupère les fournisseurs pour la barre déroulante
$fournisseurs = $pdo->query("SELECT * FROM fournisseurs ORDER BY nom ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $id_cat = $_POST['categorie'];
    $id_fourn = !empty($_POST['id_fournisseur']) ? $_POST['id_fournisseur'] : NULL; 
    $origine = !empty($_POST['origine']) ? htmlspecialchars($_POST['origine']) : 'AfroStyle';
    $prix = $_POST['prix'];
    $prix_promo = !empty($_POST['prix_promo']) ? $_POST['prix_promo'] : NULL;
    $stock = $_POST['stock'];
    $desc = htmlspecialchars($_POST['description']);
    
    // Gestion de l'upload d'image
    $chemin_db = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $nom_image = time() . '_' . str_replace(' ', '_', $_FILES['image']['name']);
        $destination = '../public/Images/' . $nom_image;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $chemin_db = 'public/Images/' . $nom_image;
        }
    }

    if ($nom && $prix && $chemin_db) {
        // SQL : On insère TOUTES les informations, y compris l'origine et le fournisseur
        $sql = "INSERT INTO produit (nom_produit, id_categorie, id_fournisseur, origine_modele, prix_unitaire, prix_promo, quantite_stock, description_produit, image_produit) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $id_cat, $id_fourn, $origine, $prix, $prix_promo, $stock, $desc, $chemin_db]);
        
        header('Location: produits.php?msg=success');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un vêtement - AfroStyle Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 850px;">
            <a href="produits.php" class="btn btn-outline-dark rounded-pill shadow-sm">
                <i class="bi bi-arrow-left"></i> Retour au catalogue
            </a>
            <h4 class="fw-bold m-0">Nouveau Produit</h4>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 bg-white p-5 rounded-4 shadow-sm">
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Nom du vêtement</label>
                            <input type="text" name="nom" class="form-control form-control-lg border-2" placeholder="ex: Grand Boubou Comorien" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Catégorie</label>
                            <select name="categorie" class="form-select form-select-lg border-2" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_categorie'] ?>"><?= $c['nom_categorie'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Origine / Style</label>
                                <input type="text" name="origine" class="form-control" placeholder="ex: Sénégalais, Tradition Moroni...">
                                <small class="text-muted">Ce qui sera affiché aux clients.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary"><i class="bi bi-truck me-1"></i>Fournisseur / Partenaire</label>
                                <select name="id_fournisseur" class="form-select border-primary-subtle" required>
                                    <option value="">-- Qui est le fournisseur ? --</option>
                                    <?php foreach($fournisseurs as $f): ?>
                                        <option value="<?= $f['id_fournisseur'] ?>">
                                            <?= htmlspecialchars(strtoupper($f['nom'])) ?> <?= htmlspecialchars($f['prenom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Pour votre gestion interne (ex: Mamadaly).</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prix (FCFA)</label>
                            <input type="number" name="prix" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success">Prix Promo</label>
                            <input type="number" name="prix_promo" class="form-control border-success-subtle" placeholder="Optionnel">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Quantité Stock</label>
                            <input type="number" name="stock" class="form-control" value="1">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description du produit</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Détails sur le tissu, la coupe, etc."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Image principale</label>
                        <div class="input-group">
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <label class="input-group-text bg-dark text-white"><i class="bi bi-image"></i></label>
                        </div>
                    </div>

                    <div class="d-grid pt-3 border-top">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill fw-bold shadow">
                            <i class="bi bi-plus-circle me-2"></i>AJOUTER AU CATALOGUE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>