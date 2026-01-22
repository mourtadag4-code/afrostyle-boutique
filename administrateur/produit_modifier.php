<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// Vérification de l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: produits.php');
    exit();
}

$id = (int)$_GET['id'];

// On récupère les infos actuelles du produit
$stmt = $pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: produits.php');
    exit();
}

// Catégories pour le select
$categories = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $id_cat = $_POST['categorie'];
    $prix = $_POST['prix'];
    $prix_promo = !empty($_POST['prix_promo']) ? $_POST['prix_promo'] : NULL;
    $stock = $_POST['stock'];
    $desc = $_POST['description'];
    
    // Par défaut, on garde l'image actuelle
    $image_finale = $produit['image_produit'];

    // Si on change l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        
        // --- MODIFICATION : Suppression de time() et nettoyage des espaces ---
        $nom_image = str_replace(' ', '_', $_FILES['image']['name']);
        $destination = '../public/Images/' . $nom_image;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            
            // --- NETTOYAGE : Supprimer l'ancienne image seulement si le nom change ---
            if (!empty($produit['image_produit'])) {
                $ancien_chemin = "../" . $produit['image_produit'];
                // On vérifie si le fichier existe et si on n'est pas en train d'écraser le même nom
                if (file_exists($ancien_chemin) && $produit['image_produit'] != 'public/Images/' . $nom_image) {
                    unlink($ancien_chemin);
                }
            }
            $image_finale = 'public/Images/' . $nom_image;
        }
    }

    // MISE À JOUR SQL
    $sql = "UPDATE produit SET 
            nom_produit = ?, id_categorie = ?, prix_unitaire = ?, 
            prix_promo = ?, quantite_stock = ?, description_produit = ?, image_produit = ? 
            WHERE id_produit = ?";
            
    $pdo->prepare($sql)->execute([$nom, $id_cat, $prix, $prix_promo, $stock, $desc, $image_finale, $id]);
    
    header('Location: produits.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le produit - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styleadmin.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 bg-white p-5 rounded-4 shadow-sm">
                <h2 class="fw-bold mb-4 text-center text-primary">Modifier l'article</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom du vêtement</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($produit['nom_produit']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Catégorie</label>
                            <select name="categorie" class="form-select">
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_categorie'] ?>" <?= ($c['id_categorie'] == $produit['id_categorie']) ? 'selected' : '' ?>>
                                        <?= $c['nom_categorie'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prix Normal (FCFA)</label>
                            <input type="number" name="prix" class="form-control" value="<?= $produit['prix_unitaire'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">Prix Promo (Optionnel)</label>
                            <input type="number" name="prix_promo" class="form-control border-danger" value="<?= $produit['prix_promo'] ?>" placeholder="Ex: 8000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Stock disponible</label>
                            <input type="number" name="stock" class="form-control" value="<?= $produit['quantite_stock'] ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($produit['description_produit']) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block">Visuel actuel</label>
                        <?php if (!empty($produit['image_produit'])): ?>
                            <img src="../<?= $produit['image_produit'] ?>" width="120" class="rounded border p-1 mb-2 bg-light shadow-sm">
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted italic">Le nom sera nettoyé (ex: Mon Image.png -> Mon_Image.png).</small>
                    </div>
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="produits.php" class="btn btn-light">Retour</a>
                        <button type="submit" class="btn btn-primary px-5 shadow">Mettre à jour l'article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>