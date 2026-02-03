<!DOCTYPE html>
<html lang="fr" id="html-tag">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.6">
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <h4 class="text-white fw-bold">AFROSTYLE</h4>
        <small class="text-white-50 small">Administration</small>
        <hr class="text-white opacity-25">
    </div>
    <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="produits.php"><i class="bi bi-box-seam me-2"></i> Produits</a>
    <a href="fournisseurs.php"><i class="bi bi-truck me-2"></i> Fournisseurs</a> <a href="promotions.php"><i class="bi bi-megaphone me-2"></i> Promotions</a>
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
                <li><a class="dropdown-item text-danger" href="deconnexion.php"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a></li>
            </ul>
        </div>
    </div>