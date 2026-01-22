<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

// --- 1. CONFIGURATION DES DATES ---
$today = date('Y-m-d');

// --- 2. COMPTAGE DES DONNÉES ---
// Compte tous les produits
$nb_produits = $pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();

// Compte tous les clients
$nb_clients = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'")->fetchColumn();

// --- 3. COMPTER TOUTES LES PROMOTIONS (SYNCHRONISÉ) ---
// On retire le filtre de date pour que le chiffre corresponde exactement 
// au nombre de lignes que vous voyez dans votre page promotions.php
$nb_promos = $pdo->query("SELECT COUNT(*) FROM promotion")->fetchColumn();

// --- 4. RÉCUPÉRATION DES REVENUS POUR LE GRAPHIQUE ---
$sql_stats = "SELECT 
                DATE_FORMAT(date_commande, '%b') as mois, 
                SUM(montant_total) as total 
              FROM commande 
              GROUP BY MONTH(date_commande)
              ORDER BY date_commande ASC 
              LIMIT 6";
$stats_query = $pdo->query($sql_stats);
$labels_mois = [];
$donnees_revenus = [];

while($row = $stats_query->fetch()) {
    $labels_mois[] = $row['mois'];
    $donnees_revenus[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.5">
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <h4 class="text-white fw-bold">AFROSTYLE</h4>
        <small class="text-white-50 small">Administration</small>
        <hr class="text-white opacity-25">
    </div>
    <a href="index.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="produits.php"><i class="bi bi-box-seam me-2"></i> Produits</a>
    <a href="promotions.php"><i class="bi bi-megaphone me-2"></i> Promotions</a>
    <a href="clients.php"><i class="bi bi-people me-2"></i> Clients</a>
    <a href="commandes.php"><i class="bi bi-cart-check me-2"></i> Commandes</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-end align-items-center mb-4 gap-3">
        <button class="btn btn-outline-dark rounded-circle shadow-sm" id="theme-toggle">
            <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
        </button>

        <div class="dropdown">
            <button class="btn bg-white rounded-pill shadow-sm d-flex align-items-center border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <div class="avatar bg-dark text-warning rounded-circle d-flex align-items-center justify-content-center me-2" style="width:30px; height:30px; font-weight:bold;">
                    <?= strtoupper(substr($_SESSION['user_nom'] ?? 'A', 0, 1)) ?>
                </div>
                <span class="fw-bold text-dark me-1"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li class="px-3 py-1 small text-muted">ADMINISTRATEUR</li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person me-2"></i> Mon Profil</a></li>
                <li><a class="dropdown-item" href="parametre.php"><i class="bi bi-gear me-2"></i> Paramètres</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="deconnexion.php"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a></li>
            </ul>
        </div>
    </div>

    <h2 class="fw-bold mb-4">Tableau de Bord</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h6 class="text-muted small uppercase fw-bold">Total Produits</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold m-0"><?= $nb_produits ?></h2>
                    <i class="bi bi-box-seam fs-1 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h6 class="text-muted small uppercase fw-bold">Promotions Enregistrées</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold m-0"><?= $nb_promos ?></h2>
                    <i class="bi bi-megaphone fs-1 text-danger opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h6 class="text-muted small uppercase fw-bold">Clients Inscrits</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold m-0"><?= $nb_clients ?></h2>
                    <i class="bi bi-people fs-1 text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Évolution des revenus (FCFA)</h5>
            <canvas id="revenueChart" style="width: 100%; max-height: 300px;"></canvas>
        </div>
    </div>

    <div class="bg-white p-4 rounded-4 shadow-sm text-center border-top border-4 border-warning">
        <h4 class="fw-bold">Ravi de vous revoir, <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?> !</h4>
        <p class="text-muted">Gérez votre boutique AfroStyle en toute simplicité.</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="produits.php" class="btn btn-dark px-4 rounded-pill">Gérer Produits</a>
            <a href="commandes.php" class="btn btn-outline-dark px-4 rounded-pill">Voir Commandes</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const labels = <?= json_encode($labels_mois); ?>;
    const dataVentes = <?= json_encode($donnees_revenus); ?>;

    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['Aucune donnée'],
            datasets: [{
                label: 'Revenus Mensuels',
                data: dataVentes.length ? dataVentes : [0],
                borderColor: '#D4AF37',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Gestion du thème Dark/Light
    const toggleBtn = document.getElementById('theme-toggle');
    const htmlTag = document.getElementById('html-tag');
    const themeIcon = document.getElementById('theme-icon');

    function applyTheme(theme) {
        if (theme === 'dark') {
            htmlTag.setAttribute('data-theme', 'dark');
            themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        } else {
            htmlTag.removeAttribute('data-theme');
            themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
        }
    }

    applyTheme(localStorage.getItem('theme'));

    toggleBtn.addEventListener('click', () => {
        const currentTheme = htmlTag.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });
</script>
</body>
</html>