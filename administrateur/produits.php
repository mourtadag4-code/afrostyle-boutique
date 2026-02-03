<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// Requête SQL : Produits + Catégories + Promotions actives
$sql = "SELECT p.*, c.nom_categorie, 
               pr.pourcentage_reduction, pr.date_fin as promo_fin
        FROM produit p 
        LEFT JOIN categorie_produit c ON p.id_categorie = c.id_categorie 
        LEFT JOIN promotion pr ON p.id_produit = pr.id_produit 
             AND CURDATE() BETWEEN pr.date_debut AND pr.date_fin
        ORDER BY p.id_produit DESC";

$produits = $pdo->query($sql)->fetchAll(); // Elle exécute la requête SQL et récupère tous les résultats dans un tableau PHP.

// Gestion des messages de notification
$alert = "";
if(isset($_GET['msg'])){
    if($_GET['msg'] == 'success') $alert = "<div class='alert alert-success border-0 shadow-sm rounded-4 text-center'>✅ Produit ajouté avec succès !</div>";
    if($_GET['msg'] == 'updated') $alert = "<div class='alert alert-info border-0 shadow-sm rounded-4 text-white text-center' style='background-color: #0d6efd;'>🔄 Mise à jour effectuée !</div>";
}

include 'header_admin.php'; 
?>

<div class="container-fluid py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-1">Catalogue Produits</h2>
        <p class="text-muted">Gestion de votre inventaire et promotions en temps réel</p>
        <div class="mx-auto mb-4" style="width: 60px; height: 4px; background: #000; border-radius: 10px;"></div>
        
        <a href="produit_ajouter.php" class="btn btn-dark rounded-pill px-5 shadow-sm py-2">
            <i class="bi bi-plus-lg me-2"></i> Ajouter un article
        </a>
    </div>

    <?= $alert ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark border-0">
                    <tr>
                        <th class="ps-4 py-3">Image</th>
                        <th class="py-3">Désignation</th>
                        <th class="py-3">Catégorie</th>
                        <th class="py-3">Prix (Achat/Promo)</th>
                        <th class="py-3 text-center">Stock</th>
                        <th class="text-center pe-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($produits as $p): ?>
                    <tr>
                        <td class="ps-4">
                            <?php if(!empty($p['image_produit'])): ?>
                                <img src="../<?= $p['image_produit'] ?>" width="50" height="50" class="rounded-3 shadow-sm object-fit-cover border">
                            <?php else: ?>
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border" style="width:50px; height:50px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($p['nom_produit']) ?></div>
                            <span class="text-muted small">ID: #<?= $p['id_produit'] ?></span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border shadow-sm px-3 rounded-pill fw-normal">
                                <?= htmlspecialchars($p['nom_categorie'] ?? 'Inconnue') ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($p['pourcentage_reduction'])): ?>
                                <?php 
                                    $reduction = $p['pourcentage_reduction'];
                                    $prix_final = $p['prix_unitaire'] * (1 - $reduction / 100);
                                ?>
                                <div class="d-flex flex-column">
                                    <span class="text-muted text-decoration-line-through small" style="font-size: 0.75rem;">
                                        <?= number_format($p['prix_unitaire'], 0, '', ' ') ?> F
                                    </span>
                                    <span class="text-danger fw-bold">
                                        <?= number_format($prix_final, 0, '', ' ') ?> FCFA
                                        <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">-<?= (int)$reduction ?>%</span>
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="fw-bold"><?= number_format($p['prix_unitaire'], 0, '', ' ') ?> FCFA</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php $stock = $p['quantite_stock']; ?>
                            <?php if($stock <= 2): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 rounded-pill">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $stock ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 rounded-pill">
                                    <?= $stock ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="produit_modifier.php?id=<?= $p['id_produit'] ?>" class="btn btn-white btn-sm px-3" title="Modifier">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </a>
                                <a href="produit_supprimer.php?id=<?= $p['id_produit'] ?>" 
                                   class="btn btn-white btn-sm px-3 border-start" 
                                   onclick="return confirm('Supprimer définitivement cet article ?')">
                                    <i class="bi bi-trash3 text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>