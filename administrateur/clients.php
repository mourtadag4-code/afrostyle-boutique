<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// 1. LOGIQUE PHP 
$sql = "SELECT * FROM utilisateurs WHERE role = 'client' ORDER BY date_inscription DESC";
$clients = $pdo->query($sql)->fetchAll();

// 2. INCLUSION DU HEADER
include 'header_admin.php'; 
?>

<div class="container-fluid py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-1">Répertoire Clients</h2>
        <p class="text-muted">Liste des utilisateurs inscrits sur la boutique</p>
        <div class="mx-auto mb-3" style="width: 60px; height: 4px; background: #000; border-radius: 10px;"></div>
        <span class="badge bg-dark rounded-pill px-4 py-2 shadow-sm">
            Total : <?= count($clients) ?> clients inscrits
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3">Nom & Prénom</th>
                        <th class="py-3">Contact</th>
                        <th class="py-3 text-center">Localisation</th>
                        <th class="py-3 text-center">Sexe</th>
                        <th class="py-3 text-center">Inscription</th>
                        <th class="text-center pe-4 py-3">Statut</th>
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
                            <div class="small"><i class="bi bi-envelope me-1 text-primary"></i> <?= htmlspecialchars($c['email']) ?></div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1 text-success"></i> <?= htmlspecialchars($c['telephone']) ?></div>
                        </td>
                        <td class="text-center">
                            <span class="small text-truncate d-inline-block" style="max-width: 150px;">
                                <i class="bi bi-geo-alt me-1 text-muted"></i>
                                <?= htmlspecialchars($c['adresse'] ?: 'Non renseignée') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if($c['sexe'] == 'F'): ?>
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3">Femme</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3">Homme</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="small fw-bold"><?= date('d/m/Y', strtotime($c['date_inscription'])) ?></div>
                            <div class="text-muted small" style="font-size: 0.7rem;"><?= date('H:i', strtotime($c['date_inscription'])) ?></div>
                        </td>
                        <td class="text-center pe-4">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">
                                <i class="bi bi-check-circle-fill me-1"></i> Actif
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($clients)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 d-block mb-3 opacity-25"></i>
                            Aucun client inscrit pour le moment.
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