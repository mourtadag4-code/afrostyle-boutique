<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// On vérifie si on a bien un ID de commande dans l'URL
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: commandes.php');
    exit();
}

$id_commande = (int)$_GET['id'];

// 1. On récupère les infos de la commande et du client
$stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email, u.telephone, u.adresse 
                       FROM commande c 
                       JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur 
                       WHERE c.id_commande = ?");
$stmt->execute([$id_commande]);
$commande = $stmt->fetch();

if (!$commande) {
    die("Commande introuvable.");
}

// 2. On récupère les articles
$stmt = $pdo->prepare("SELECT d.*, p.nom_produit, p.image_produit 
                       FROM details_commande d 
                       JOIN produit p ON d.id_produit = p.id_produit 
                       WHERE d.id_commande = ?");
$stmt->execute([$id_commande]);
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr" id="html-tag"> <head>
    <meta charset="UTF-8">
    <title>Détails Commande #<?= $id_commande ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css">
    <style>
        /* Style spécifique pour l'impression : on force le mode clair */
        @media print {
            #theme-toggle, .btn-outline-dark { display: none !important; }
            body { background: white !important; color: black !important; }
            .card { border: 1px solid #ccc !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="commandes.php" class="btn btn-outline-dark rounded-pill shadow-sm me-2">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <button id="theme-toggle" class="btn btn-outline-dark rounded-circle shadow-sm">
                <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
            </button>
        </div>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow">
            <i class="bi bi-printer me-2"></i> Imprimer Facture
        </button>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-3">
                    <h6 class="mb-0">Informations Client</h6>
                </div>
                <div class="card-body bg-card-custom">
                    <h5 class="fw-bold text-dark-emphasis"><?= htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']) ?></h5>
                    <p class="text-muted small mb-3">ID Client: #<?= $commande['id_utilisateur'] ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= $commande['email'] ?></p>
                    <p class="mb-3"><strong>Tél:</strong> <?= $commande['telephone'] ?></p>
                    <div class="p-3 bg-light text-dark rounded-3 border">
                        <small class="text-muted d-block">Adresse de livraison :</small>
                        <strong><?= nl2br(htmlspecialchars($commande['adresse'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden admin-table-card">
                <div class="card-header bg-white p-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">Articles commandés</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Produit</th>
                                    <th>Prix Unit.</th>
                                    <th>Qté</th>
                                    <th class="text-end pe-4">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($articles as $art): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="../<?= $art['image_produit'] ?>" width="45" height="45" class="rounded me-3 shadow-sm" style="object-fit:cover; border: 1px solid var(--border-color);">
                                            <span class="fw-bold text-dark-emphasis"><?= htmlspecialchars($art['nom_produit']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($art['prix_unitaire'], 0, '', ' ') ?> FCFA</td>
                                    <td>x<?= $art['Quantite_commande'] ?></td>
                                    <td class="text-end pe-4 fw-bold text-dark-emphasis">
                                        <?= number_format($art['prix_unitaire'] * $art['Quantite_commande'], 0, '', ' ') ?> FCFA
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end py-3 fw-bold">TOTAL À PAYER :</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-primary fs-5">
                                        <?= number_format($commande['montant_total'], 0, '', ' ') ?> FCFA
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../public/js/script.js"></script> </body>
</html>