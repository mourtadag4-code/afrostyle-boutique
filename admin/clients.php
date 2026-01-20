<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// On récupère uniquement les utilisateurs qui sont des clients
$sql = "SELECT * FROM utilisateurs WHERE role = 'client' ORDER BY date_inscription DESC";
$clients = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag"> <head>
    <meta charset="UTF-8">
    <title>Gestion Clients - AfroStyle Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.7">
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
        <a href="clients.php" class="active"><i class="bi bi-people me-2"></i> Clients</a>
        <a href="commandes.php"><i class="bi bi-cart-check me-2"></i> Commandes</a>
        <hr class="text-secondary mx-3">
        <a href="deconnexion.php" class="text-danger mt-5"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Répertoire Clients</h2>
        <button class="btn btn-outline-dark rounded-circle shadow-sm" id="theme-toggle">
            <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0">Liste des utilisateurs inscrits sur la boutique</p>
            <span class="badge bg-dark rounded-pill px-3 py-2">Total : <?= count($clients) ?> clients</span>
        </div>

        <div class="admin-table-card shadow-sm bg-white rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Nom & Prénom</th>
                            <th>Contact</th>
                            <th>Localisation</th>
                            <th>Sexe</th>
                            <th>Inscription</th>
                            <th class="text-center pe-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clients as $c): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark-emphasis"><?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?></div>
                                <span class="text-muted x-small">ID: #<?= $c['id_utilisateur'] ?></span>
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($c['email']) ?></div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($c['telephone']) ?></div>
                            </td>
                            <td>
                                <span class="small text-truncate d-inline-block" style="max-width: 150px;">
                                    <?= htmlspecialchars($c['adresse'] ?: 'Non renseignée') ?>
                                </span>
                            </td>
                            <td>
                                <?php if($c['sexe'] == 'F'): ?>
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Femme</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Homme</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small"><?= date('d/m/Y', strtotime($c['date_inscription'])) ?></div>
                            </td>
                            <td class="text-center pe-4">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Actif</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($clients)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-3"></i>
                                Aucun client inscrit pour le moment.
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