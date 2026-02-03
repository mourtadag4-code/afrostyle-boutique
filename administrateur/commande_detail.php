<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: commandes.php');
    exit();
}

$id_commande = (int)$_GET['id'];

// 1. Récupération des infos commande et client
$stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email, u.telephone, u.adresse 
                       FROM commande c 
                       JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur 
                       WHERE c.id_commande = ?");
$stmt->execute([$id_commande]);
$commande = $stmt->fetch();

if (!$commande) {
    die("Commande introuvable.");
}

// 2. Récupération des articles
$stmt = $pdo->prepare("SELECT d.*, p.nom_produit, p.image_produit 
                       FROM details_commande d 
                       JOIN produit p ON d.id_produit = p.id_produit 
                       WHERE d.id_commande = ?");
$stmt->execute([$id_commande]);
$articles = $stmt->fetchAll();

include 'header_admin.php'; 
?>

<style>
    /* Style pour l'impression factures */
    @media print {
        .sidebar, #theme-toggle, .btn-print-hide, .dropdown { display: none !important; }
        .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        body { background: white !important; color: black !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
        .container { max-width: 100% !important; width: 100% !important; }
    }
</style>

<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center btn-print-hide">
        <div>
            <a href="commandes.php" class="btn btn-outline-dark rounded-pill shadow-sm me-2">
                <i class="bi bi-arrow-left"></i> Retour aux commandes
            </a>
        </div>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow">
            <i class="bi bi-printer me-2"></i> Imprimer la Facture
        </button>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white p-3">
                    <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informations Client</h6>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold"><?= htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']) ?></h5>
                    <p class="text-muted small mb-3">Client #<?= $commande['id_utilisateur'] ?></p>
                    <hr>
                    <p class="mb-1"><strong>Email:</strong> <?= $commande['email'] ?></p>
                    <p class="mb-3"><strong>Tél:</strong> <?= $commande['telephone'] ?></p>
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-muted d-block mb-1">Adresse de livraison :</small>
                        <span class="fw-semibold"><?= nl2br(htmlspecialchars($commande['adresse'])) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-secondary text-white p-3">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>État de la commande</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1">Date : <strong><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></strong></p>
                    <p class="mb-0">ID Commande : <strong>#<?= $id_commande ?></strong></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white p-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-cart3 me-2"></i>Détails des articles</h6>
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
                                            <img src="../<?= $art['image_produit'] ?>" width="50" height="50" class="rounded me-3 shadow-sm border" style="object-fit:cover;">
                                            <span class="fw-bold"><?= htmlspecialchars($art['nom_produit']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($art['prix_unitaire'], 0, '', ' ') ?> FCFA</td>
                                    <td>x<?= $art['Quantite_commande'] ?></td>
                                    <td class="text-end pe-4 fw-bold">
                                        <?= number_format($art['prix_unitaire'] * $art['Quantite_commande'], 0, '', ' ') ?> FCFA
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light border-top border-2">
                                <tr>
                                    <td colspan="3" class="text-end py-3 fw-bold">TOTAL GÉNÉRAL :</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-primary fs-5">
                                        <?= number_format($commande['montant_total'], 0, '', ' ') ?> FCFA
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 btn-print-hide">
                <small class="text-dark"><i class="bi bi-exclamation-triangle-fill me-2"></i>Note : En cas de modification, veuillez mettre à jour le stock manuellement dans la gestion des produits.</small>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>