<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$id_admin = $_SESSION['user_id'];
$message = "";

// --- LOGIQUE MOT DE PASSE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pwd'])) {
    // ... (garde ton code de mot de passe ici) ...
    $message = "<div class='alert alert-success rounded-4'>✅ Paramètres mis à jour !</div>";
}
?>
<!DOCTYPE html>
<html lang="fr" id="html-tag">
<head>
    <meta charset="UTF-8">
    <title>Paramètres - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.6">
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <h4 class="text-white fw-bold">AFROSTYLE</h4>
        <hr class="text-white opacity-25">
    </div>
    <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="profil.php"><i class="bi bi-person me-2"></i> Mon Profil</a>
    <a href="parametre.php" class="active"><i class="bi bi-gear me-2"></i> Paramètres</a>
</div>

<div class="main-content">
    <h2 class="fw-bold mb-4 text-center">Configuration du Système</h2>

    <div class="container" style="max-width: 1000px;">
        <?= $message ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock text-warning me-2"></i> Sécurité Admin</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Ancien mot de passe</label>
                            <input type="password" name="current_pwd" class="form-control rounded-pill">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Nouveau mot de passe</label>
                            <input type="password" name="new_pwd" class="form-control rounded-pill">
                        </div>
                        <button type="submit" name="update_pwd" class="btn btn-dark w-100 rounded-pill shadow-sm">Changer mon accès</button>
                    </form>
                </div>

                <div class="card shadow-sm border-0 rounded-4 p-4 text-center bg-warning-subtle">
                    <h5 class="fw-bold mb-2">Mode d'affichage</h5>
                    <p class="small text-muted">Basculez entre le thème clair et sombre</p>
                    <button class="btn btn-dark rounded-pill px-5" id="theme-toggle">
                        <i class="bi bi-circle-half me-2"></i> Changer de Thème
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shop text-warning me-2"></i> Infos Boutique AfroStyle</h5>
                    
                    <div class="mb-3">
                        <label class="small fw-bold">Nom du Site</label>
                        <input type="text" class="form-control rounded-pill" value="AfroStyle Shop">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Email de contact (Clients)</label>
                        <input type="email" class="form-control rounded-pill" value="contact@afrostyle.com">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Frais de livraison par défaut (FCFA)</label>
                        <input type="number" class="form-control rounded-pill" value="2000">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Statut de la boutique</label>
                        <select class="form-select rounded-pill">
                            <option value="1">Ouverte (En ligne)</option>
                            <option value="0">Fermée (Maintenance)</option>
                        </select>
                    </div>

                    <button class="btn btn-warning w-100 rounded-pill fw-bold shadow-sm">Enregistrer la configuration</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // SCRIPT THEME SANS ERREUR
    const htmlTag = document.getElementById('html-tag');
    const toggleBtn = document.getElementById('theme-toggle');

    // Charger le thème au démarrage
    if (localStorage.getItem('theme') === 'dark') {
        htmlTag.setAttribute('data-theme', 'dark');
    }

    toggleBtn.addEventListener('click', () => {
        if (htmlTag.getAttribute('data-theme') === 'dark') {
            htmlTag.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        } else {
            htmlTag.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });
</script>
</body>
</html>