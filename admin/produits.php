<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// Requête SQL complète : on lie les produits, les catégories et les promotions actives
$sql = "SELECT p.*, c.nom_categorie, 
               pr.pourcentage_reduction, pr.date_fin as promo_fin
        FROM produit p 
        LEFT JOIN categorie_produit c ON p.id_categorie = c.id_categorie 
        LEFT JOIN promotion pr ON p.id_produit = pr.id_produit 
             AND CURDATE() BETWEEN pr.date_debut AND pr.date_fin
        ORDER BY p.id_produit DESC";

$produits = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Stock - AfroStyle Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.9">
</head>
<body>

<div class="sidebar">
    <div class="logo-area text-center py-4">
        <h4 class="fw-bold text-white mb-0" style="color: #D4AF37 !important;">AFROSTYLE</h4>
        <small class="text-white-50">Administration</small>
    </div>
    <div class="mt-3">
        <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="produits.php" class="active"><i class="bi bi-box-seam me-2"></i> Produits</a>
        <a href="promotions.php"><i class="bi bi-megaphone me-2"></i> Promotions</a>
        <a href="clients.php"><i class="bi bi-people me-2"></i> Clients</a>
        <a href="commandes.php"><i class="bi bi-cart-check me-2"></i> Commandes</a>
        <hr class="text-secondary mx-3">
        <a href="deconnexion.php" class="text-danger mt-5"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Catalogue Produits</h2>
        <button class="btn btn-outline-dark rounded-circle shadow-sm" id="theme-toggle">
            <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0">Gestion de votre inventaire et promotions en temps réel</p>
            <a href="produit_ajouter.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Ajouter un article
            </a>
        </div>

        <div class="admin-table-card shadow-sm bg-white rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Image</th>
                            <th>Désignation</th>
                            <th>Catégorie</th>
                            <th>Prix (Achat/Promo)</th>
                            <th>Stock</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($produits as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if(!empty($p['image_produit'])): ?>
                                    <img src="../<?= $p['image_produit'] ?>" width="55" height="55" class="rounded shadow-sm" style="object-fit: cover; border: 1px solid #eee;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:55px; height:55px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark-emphasis"><?= htmlspecialchars($p['nom_produit']) ?></div>
                                <span class="text-muted small">Ref: #<?= $p['id_produit'] ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border shadow-sm"><?= htmlspecialchars($p['nom_categorie'] ?? 'Inconnue') ?></span>
                            </td>
                            <td>
                                <?php if (!empty($p['pourcentage_reduction'])): ?>
                                    <?php 
                                        $reduction = $p['pourcentage_reduction'];
                                        $prix_final = $p['prix_unitaire'] * (1 - $reduction / 100);
                                    ?>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted text-decoration-line-through small">
                                            <?= number_format($p['prix_unitaire'], 0, '', ' ') ?> F
                                        </span>
                                        <span class="text-danger fw-bold">
                                            <?= number_format($prix_final, 0, '', ' ') ?> FCFA
                                            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.65rem;">-<?= (int)$reduction ?>%</span>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="fw-bold"><?= number_format($p['prix_unitaire'], 0, '', ' ') ?> FCFA</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $stock = $p['quantite_stock']; ?>
                                <?php if($stock <= 2): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 rounded-pill">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $stock ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 rounded-pill">
                                        <?= $stock ?> en stock
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <a href="produit_modifier.php?id=<?= $p['id_produit'] ?>" class="btn btn-white btn-sm border" title="Modifier">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                    <a href="produit_supprimer.php?id=<?= $p['id_produit'] ?>" 
                                       class="btn btn-white btn-sm border" 
                                       onclick="return confirm('Supprimer cet article ?')">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../public/js/script.js"></script>
</body>
</html>