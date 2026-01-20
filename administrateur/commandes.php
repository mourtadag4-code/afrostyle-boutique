<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$sql = "SELECT c.*, u.nom, u.prenom 
        FROM commande c
        JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
        ORDER BY c.date_commande DESC";
$commandes = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag"> <head>
    <meta charset="UTF-8">
    <title>Suivi Commandes - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.8">
</head>
<body>

<div class="sidebar">
    <div class="logo-area text-center py-4">
        <h4 class="fw-bold text-white mb-0" style="color: #D4AF37 !important;">AFROSTYLE</h4>
        <small class="text-white-50">Administration</small>
    </div>
    <div class="mt-3">
        <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="produits.php"><i class="bi bi-box-seam me-2"></i> Produits</a>
        <a href="clients.php"><i class="bi bi-people me-2"></i> Clients</a>
        <a href="commandes.php" class="active"><i class="bi bi-cart-check me-2"></i> Commandes</a>
        <hr class="text-secondary mx-3">
        <a href="deconnexion.php" class="text-danger mt-5"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Suivi des Commandes</h2>
            <button class="btn btn-outline-dark rounded-circle shadow-sm" id="theme-toggle">
                <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
            </button>
        </div>

        <div class="admin-table-card shadow-sm bg-white rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">N° Commande</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Montant Total</th>
                            <th>Statut</th>
                            <th class="text-center pe-4">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($commandes as $com): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?= $com['id_commande'] ?></td>
                            <td class="small"><?= date('d/m/Y H:i', strtotime($com['date_commande'])) ?></td>
                            <td class="fw-bold text-dark-emphasis"><?= htmlspecialchars($com['nom'] . ' ' . $com['prenom']) ?></td>
                            <td class="fw-bold text-primary"><?= number_format($com['montant_total'], 0, '', ' ') ?> FCFA</td>
                            <td>
                                <?php 
                                $st = $com['statut_commande'];
                                $class = "bg-secondary"; 
                                
                                if($st == 'en_attente') $class = "bg-warning text-dark";
                                if($st == 'validee')    $class = "bg-primary";
                                if($st == 'expediee')   $class = "bg-info";
                                if($st == 'livree')     $class = "bg-success";
                                if($st == 'annulee')    $class = "bg-danger";
                                ?>
                                <span class="badge <?= $class ?> px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem;">
                                    <?= str_replace('_', ' ', strtoupper($st)) ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="commande_detail.php?id=<?= $com['id_commande'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Voir
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($commandes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                                Aucune commande enregistrée.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../public/js/script.js"></script> </body>
</html>