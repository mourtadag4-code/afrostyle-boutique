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

// 3. RÉCUPÉRATION DES LISTES POUR LES SELECTS
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
    
    $image_finale = $produit['image_produit'];

    // Logique de remplacement d'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $nom_image = time() . '_' . str_replace(' ', '_', $_FILES['image']['name']);
        $destination = '../public/Images/' . $nom_image;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            // Suppression de l'ancienne image si elle existe
            if (!empty($produit['image_produit']) && file_exists("../" . $produit['image_produit'])) {
                unlink("../" . $produit['image_produit']);
            }
            $image_finale = 'public/Images/' . $nom_image;
        }
    }

    // Mise à jour de la base de données
    $sql = "UPDATE produit SET 
            nom_produit = ?, id_categorie = ?, id_fournisseur = ?, origine_modele = ?,
            prix_unitaire = ?, prix_promo = ?, quantite_stock = ?, 
            description_produit = ?, image_produit = ? 
            WHERE id_produit = ?";
            
    try {
        $pdo->prepare($sql)->execute([
            $nom, $id_cat, $id_fourn, $origine, $prix, $prix_promo, $stock, $desc, $image_finale, $id
        ]);
        header('Location: produits.php?msg=updated');
        exit();
    } catch (PDOException $e) {
        $error_msg = "Erreur lors de la modification : " . $e->getMessage();
    }
}

include 'header_admin.php'; 
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 900px;">
        <a href="produits.php" class="btn btn-outline-secondary rounded-pill shadow-sm bg-white">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
        <h4 class="fw-bold m-0">MODIFIER L'ARTICLE <span class="text-warning">#<?= $id ?></span></h4>
        <div style="width: 100px;"></div>
    </div>

    <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger mx-auto" style="max-width: 900px;"><?= $error_msg ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-9 col-xl-8">
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4">
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nom du produit</label>
                            <input type="text" name="nom" class="form-control form-control-lg border-2" value="<?= htmlspecialchars($produit['nom_produit']) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted text-uppercase">Catégorie</label>
                            <select name="categorie" class="form-select form-select-lg border-2" required>
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_categorie'] ?>" <?= ($c['id_categorie'] == $produit['id_categorie']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card bg-secondary bg-opacity-10 border-0 p-3 mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><i class="bi bi-geo-alt text-danger me-1"></i>ORIGINE</label>
                                <input type="text" name="origine" class="form-control border-0" value="<?= htmlspecialchars($produit['origine_modele']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-primary"><i class="bi bi-truck me-1"></i>FOURNISSEUR</label>
                                <select name="id_fournisseur" class="form-select border-0">
                                    <option value="">Sélectionner un fournisseur</option>
                                    <?php foreach($fournisseurs as $f): ?>
                                        <option value="<?= $f['id_fournisseur'] ?>" <?= ($f['id_fournisseur'] == $produit['id_fournisseur']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(strtoupper($f['nom'])) ?> <?= htmlspecialchars($f['prenom'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">PRIX (FCFA)</label>
                            <input type="number" name="prix" class="form-control border-2" value="<?= $produit['prix_unitaire'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-success">PRIX PROMO (SI ACTIF)</label>
                            <input type="number" name="prix_promo" class="form-control border-success-subtle border-2" value="<?= $produit['prix_promo'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">STOCK ACTUEL</label>
                            <input type="number" name="stock" class="form-control border-2" value="<?= $produit['quantite_stock'] ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">DESCRIPTION</label>
                        <textarea name="description" class="form-control border-2" rows="3"><?= htmlspecialchars($produit['description_produit']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small d-block mb-3">VISUEL DU PRODUIT</label>
                        <div class="d-flex align-items-center gap-4 p-3 border rounded-3 bg-light bg-opacity-25">
                            <div class="text-center">
                                <?php if(!empty($produit['image_produit'])): ?>
                                    <img src="../<?= $produit['image_produit'] ?>" width="100" height="100" class="rounded shadow-sm border p-1 bg-white object-fit-cover">
                                <?php else: ?>
                                    <div class="bg-white border rounded d-flex align-items-center justify-content-center" style="width:100px; height:100px;"><i class="bi bi-image"></i></div>
                                <?php endif; ?>
                                <div class="small text-muted mt-1">Actuelle</div>
                            </div>
                            
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-2 font-italic">Remplacer l'image :</p>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-warning btn-lg rounded-pill fw-bold shadow">
                            <i class="bi bi-arrow-repeat me-2"></i>ENREGISTRER LES MODIFICATIONS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>