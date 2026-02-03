<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$id_admin = $_SESSION['user_id'];
$message = "";

// --- LOGIQUE MOT DE PASSE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pwd'])) {
    // Ton code de vérification et de hachage ici...
    // $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id_utilisateur = ?");
    $message = "<div class='alert alert-success rounded-4 border-0 shadow-sm'>✅ Paramètres de sécurité mis à jour !</div>";
}

// INCLUSION DU HEADER (Sidebar, Meta, CSS)
include 'header_admin.php'; 
?>

<div class="container-fluid py-2">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-0">Configuration du Système</h2>
        <p class="text-muted">Gérez les accès administrateur et les réglages de la boutique</p>
    </div>

    <div class="container" style="max-width: 1000px;">
        <?= $message ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 card">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock text-warning me-2"></i> Sécurité Admin</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Ancien mot de passe</label>
                            <input type="password" name="current_pwd" class="form-control rounded-pill bg-light border-0">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Nouveau mot de passe</label>
                            <input type="password" name="new_pwd" class="form-control rounded-pill bg-light border-0">
                        </div>
                        <button type="submit" name="update_pwd" class="btn btn-dark w-100 rounded-pill shadow-sm">
                            Changer mon accès
                        </button>
                    </form>
                </div>

                <div class="card shadow-sm border-0 rounded-4 p-4 text-center bg-warning-subtle">
                    <h5 class="fw-bold mb-2 text-dark">Mode d'affichage</h5>
                    <p class="small text-muted">Personnalisez votre interface de travail</p>
                    <button class="btn btn-dark rounded-pill px-5 shadow-sm" id="theme-toggle-alt">
                        <i class="bi bi-circle-half me-2"></i> Basculer le thème
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 card">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shop text-warning me-2"></i> Infos Boutique AfroStyle</h5>
                    
                    <div class="mb-3">
                        <label class="small fw-bold">Nom du Site</label>
                        <input type="text" class="form-control rounded-pill bg-light border-0" value="AfroStyle Shop">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Email de contact (Clients)</label>
                        <input type="email" class="form-control rounded-pill bg-light border-0" value="contact@afrostyle.com">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Frais de livraison (FCFA)</label>
                        <input type="number" class="form-control rounded-pill bg-light border-0" value="2000">
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold">Statut de la boutique</label>
                        <select class="form-select rounded-pill bg-light border-0">
                            <option value="1">Ouverte (En ligne)</option>
                            <option value="0">Fermée (Maintenance)</option>
                        </select>
                    </div>

                    <button class="btn btn-warning w-100 rounded-pill fw-bold shadow-sm py-2">
                        ENREGISTRER LA CONFIGURATION
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Petit script additionnel pour le bouton de cette page spécifique
    document.getElementById('theme-toggle-alt')?.addEventListener('click', function() {
        // On simule le clic sur le bouton principal du mode sombre (situé dans le header ou sidebar)
        document.getElementById('theme-toggle').click();
    });
</script>

<?php 
// INCLUSION DU FOOTER (JS Bootstrap, Scripts Thème)
include 'footer_admin.php'; 
?>