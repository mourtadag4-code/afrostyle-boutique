<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// 1. VÉRIFICATION DE L'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: produits.php');
    exit();
}

$id = (int)$_GET['id'];

// 2. RÉCUPÉRATION DES INFOS DU PRODUIT
$stmt = $pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: produits.php');
    exit();
}

// 3. RÉCUPÉRATION DES LISTES (CATÉGORIES ET FOURNISSEURS)
$categories = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();
$fournisseurs = $pdo->query("SELECT * FROM fournisseurs ORDER BY nom ASC")->fetchAll();

// 4. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $id_cat = $_POST['categorie'];
    $id_fourn = !empty($_POST['id_fournisseur']) ? $_POST['id_fournisseur'] : NULL;
    $origine = !empty($_POST['origine']) ? htmlspecialchars($_POST['origine']) : 'AfroStyle';
    $prix = $_POST['prix'];
    $prix_promo = !empty($_POST['prix_promo']) ? $_POST['prix_promo'] : NULL;
    $stock = $_POST['stock'];
    $desc = htmlspecialchars($_POST['description']);
    
    // Garder l'image actuelle par défaut
    $image_finale = $produit['image_produit'];

    // Si une nouvelle image est téléchargée
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $nom_image = time() . '_' . str_replace(' ', '_', $_FILES['image']['name']);
        $destination = '../public/Images/' . $nom_image;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            // Supprimer l'ancienne image du serveur si elle existe
            if (!empty($produit['image_produit']) && file_exists("../" . $produit['image_produit'])) {
                unlink("../" . $produit['image_produit']);
            }
            $image_finale = 'public/Images/' . $nom_image;
        }
    }

    // MISE À JOUR SQL
    $sql = "UPDATE produit SET 
            nom_produit = ?, id_categorie = ?, id_fournisseur = ?, origine_modele = ?,
            prix_unitaire = ?, prix_promo = ?, quantite_stock = ?, 
            description_produit = ?, image_produit = ? 
            WHERE id_produit = ?";
            
    $pdo->prepare($sql)->execute([
        $nom, $id_cat, $id_fourn, $origine, $prix, $prix_promo, $stock, $desc, $image_finale, $id
    ]);
    
    header('Location: produits.php?msg=updated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier <?= htmlspecialchars($produit['nom_produit']) ?> - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 850px;">
            <a href="produits.php" class="btn btn-outline-dark rounded-pill shadow-sm">
                <i class="bi bi-arrow-left"></i> Annuler
            </a>
            <h4 class="fw-bold m-0 text-primary">Modifier l'article #<?= $id ?></h4>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 bg-white p-5 rounded-4 shadow-sm">
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Nom du produit</label>
                            <input type="text" name="nom" class="form-control form-control-lg" value="<?= htmlspecialchars($produit['nom_produit']) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Catégorie</label>
                            <select name="categorie" class="form-select form-select-lg" required>
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_categorie'] ?>" <?= ($c['id_categorie'] == $produit['id_categorie']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Origine Culturelle</label>
                                <input type="text" name="origine" class="form-control" value="<?= htmlspecialchars($produit['origine_modele']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Fournisseur Associé</label>
                                <select name="id_fournisseur" class="form-select border-primary-subtle" required>
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach($fournisseurs as $f): ?>
                                        <option value="<?= $f['id_fournisseur'] ?>" <?= ($f['id_fournisseur'] == $produit['id_fournisseur']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(strtoupper($f['nom'])) ?> <?= htmlspecialchars($f['prenom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prix (FCFA)</label>
                            <input type="number" name="prix" class="form-control" value="<?= $produit['prix_unitaire'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Prix Promo</label>
                            <input type="number" name="prix_promo" class="form-control" value="<?= $produit['prix_promo'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Quantité en Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?= $produit['quantite_stock'] ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($produit['description_produit']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block">Image actuelle</label>
                        <div class="d-flex align-items-center gap-3">
                            <img src="../<?= $produit['image_produit'] ?>" width="100" class="rounded shadow-sm border p-1">
                            <div class="flex-grow-1">
                                <label class="small text-muted">Remplacer l'image :</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow">
                            <i class="bi bi-save me-2"></i>ENREGISTRER LES MODIFICATIONS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>