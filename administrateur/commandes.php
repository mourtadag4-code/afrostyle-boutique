<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// 1. LOGIQUE PHP 
$sql = "SELECT c.*, u.nom, u.prenom 
        FROM commande c
        JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
        ORDER BY c.date_commande DESC";
$commandes = $pdo->query($sql)->fetchAll();

// 2. INCLUSION DU HEADER
include 'header_admin.php'; 
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Suivi des Commandes</h2>
        </div>

    <div class="admin-table-card shadow-sm bg-white rounded-4 overflow-hidden card">
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

<?php 
// 3. INCLUSION DU FOOTER
include 'footer_admin.php'; 
?>