<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$currentPage = basename($_SERVER['PHP_SELF']);

// Logique Panier
$nbArticles = 0;
if (!empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $p) { $nbArticles += $p['quantite'] ?? 0; }
}
$nbFavoris = isset($_SESSION['favoris']) ? count($_SESSION['favoris']) : 0;

$isConnecte = isset($_SESSION['utilisateur_id']);
$userName = $_SESSION['utilisateur_nom'] ?? 'Mon compte';
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'AFROSTYLE SHOP' ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="public/CSS/style.css">
    
    <style>
        :root { --afro-gold: #D4AF37; }
        .nav-link { 
            display: flex !important; 
            align-items: center; 
            gap: 10px; 
            cursor: pointer; 
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;
        }
        .nav-link.active-page { color: var(--afro-gold) !important; border-bottom: 2px solid var(--afro-gold); }
        
        /* Correction Dropdown */
        .dropdown-menu { 
            border-radius: 12px; 
            border: none; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.15); 
            margin-top: 15px !important; 
        }
        .dropdown-menu.show { display: block !important; }
        
        .badge-notify { font-size: 0.65rem; position: absolute; top: -5px; right: -5px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">
            <span style="color: var(--afro-gold);">AFRO</span>STYLE
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item">
                    <a class="nav-link px-3 <?= ($currentPage == 'index.php') ? 'active-page' : '' ?>" href="index.php">
                        <i class="bi bi-house-door"></i> Accueil
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3 <?= ($currentPage == 'catalogue.php') ? 'active-page' : '' ?>" href="catalogue.php">
                        <i class="bi bi-grid"></i> Catalogue
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= ($currentPage == 'avis.php') ? 'active-page' : '' ?>" href="avis.php">
                        <i class="bi bi-chat-left-text"></i> Avis
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 <?= ($currentPage == 'contact.php') ? 'active-page' : '' ?>" href="contact.php">
                        <i class="bi bi-envelope"></i> Contact
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3" href="favoris.php">
                        <div class="position-relative">
                            <i class="bi bi-heart"></i>
                            <?php if($nbFavoris > 0): ?><span class="badge rounded-pill bg-warning text-dark badge-notify"><?= $nbFavoris ?></span><?php endif; ?>
                        </div>
                        <span>Favoris</span>
                    </a>
                </li>

                <li class="nav-item me-lg-2">
                    <a class="nav-link px-3" href="panier.php">
                        <div class="position-relative">
                            <i class="bi bi-cart3"></i>
                            <?php if($nbArticles > 0): ?><span class="badge rounded-pill bg-danger badge-notify"><?= $nbArticles ?></span><?php endif; ?>
                        </div>
                        <span>Panier</span>
                    </a>
                </li>

                <li class="nav-item px-lg-2">
                    <a class="nav-link text-warning" id="theme-toggle" href="#">
                        <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
                    </a>
                </li>

                <li class="nav-item dropdown border-lg-start ps-lg-3">
                    <?php if($isConnecte): ?>
                        <a class="nav-link dropdown-toggle" href="#" id="manualDropdownBtn">
                            <i class="bi bi-person-circle text-warning fs-5"></i>
                            <span class="fw-bold"><?= htmlspecialchars($userName) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="manualMenu">
                            <li><a class="dropdown-item py-2" href="compte.php"><i class="bi bi-person me-2"></i> Mon Profil</a></li>
                            <li><a class="dropdown-item py-2" href="commandes.php"><i class="bi bi-bag-check me-2"></i> Mes Commandes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-bold" href="deconnexion.php"><i class="bi bi-box-arrow-right me-2"></i> Déconnexion</a></li>
                        </ul>
                    <?php else: ?>
                        <a class="btn btn-outline-warning btn-sm px-4 rounded-pill fw-bold" href="connexion.php">CONNEXION</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/JS/script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('manualDropdownBtn');
    const menu = document.getElementById('manualMenu');
    if (btn && menu) {
        btn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            menu.classList.toggle('show');
        };
        document.onclick = function() { menu.classList.remove('show'); };
    }
});
</script>
</body>
</html>