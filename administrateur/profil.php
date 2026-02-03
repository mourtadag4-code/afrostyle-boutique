<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$id_admin = $_SESSION['user_id'];
$message = "";
$mode_edition = isset($_GET['edit']); 

// --- 1. TRAITEMENT DE LA MISE À JOUR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sauvegarder'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $sexe = $_POST['sexe'];

    $sql = "UPDATE utilisateurs SET nom=?, prenom=?, adresse=?, telephone=?, sexe=? WHERE id_utilisateur=?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nom, $prenom, $adresse, $telephone, $sexe, $id_admin])) {
        $_SESSION['user_nom'] = $nom;
        $message = "<div class='alert alert-success border-0 shadow-sm rounded-4 mb-4 text-center'>✅ Profil mis à jour avec succès !</div>";
        $mode_edition = false; 
    }
}

// --- 2. RÉCUPÉRATION DES DONNÉES ---
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id_admin]);
$admin = $stmt->fetch();

include 'header_admin.php'; 
?>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Mon Profil</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-dark">Admin</a></li>
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4" style="max-width: 950px;">
        <?= $message ?>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden card">
            <div class="row g-0">
                <div class="col-md-4 bg-dark text-center p-5 text-white d-flex flex-column align-items-center justify-content-center">
                    <div class="bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center mb-4 shadow-lg border border-4 border-white border-opacity-25" 
                         style="width:120px; height:120px; font-size: 3rem;">
                        <?= strtoupper(substr($admin['nom'], 0, 1)) ?>
                    </div>
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($admin['prenom']) ?> <?= htmlspecialchars($admin['nom']) ?></h4>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 text-uppercase letter-spacing-1" style="font-size: 0.7rem;">
                        <i class="bi bi-shield-check me-1"></i><?= strtoupper($admin['role']) ?>
                    </span>
                    <p class="mt-4 small text-white-50">Membre depuis : <br><strong><?= date('d/m/Y', strtotime($admin['date_inscription'] ?? 'now')) ?></strong></p>
                </div>

                <div class="col-md-8 p-4 p-md-5">
                    
                    <?php if (!$mode_edition): ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0"><i class="bi bi-info-circle me-2 text-primary"></i>Détails du compte</h5>
                            <a href="profil.php?edit=1" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                <i class="bi bi-pencil me-1"></i> Modifier
                            </a>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Email</label>
                                <span class="fw-semibold"><?= htmlspecialchars($admin['email']) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Téléphone</label>
                                <span class="fw-semibold"><?= htmlspecialchars($admin['telephone'] ?: 'Non renseigné') ?></span>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold d-block">Adresse Résidentielle</label>
                                <span class="fw-semibold"><?= htmlspecialchars($admin['adresse'] ?: 'Non renseignée') ?></span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small text-uppercase fw-bold d-block">Genre</label>
                                <span class="fw-semibold"><?= $admin['sexe'] == 'F' ? 'Femme' : 'Homme' ?></span>
                            </div>
                        </div>

                    <?php else: ?>
                        <h5 class="fw-bold mb-4 text-warning"><i class="bi bi-pencil-square me-2"></i>Mise à jour des données</h5>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold mb-1">Nom</label>
                                    <input type="text" name="nom" class="form-control border-2" value="<?= htmlspecialchars($admin['nom']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold mb-1">Prénom</label>
                                    <input type="text" name="prenom" class="form-control border-2" value="<?= htmlspecialchars($admin['prenom']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold mb-1">Adresse complète</label>
                                    <input type="text" name="adresse" class="form-control border-2" value="<?= htmlspecialchars($admin['adresse']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold mb-1">Téléphone</label>
                                    <input type="text" name="telephone" class="form-control border-2" value="<?= htmlspecialchars($admin['telephone']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold mb-1">Genre</label>
                                    <select name="sexe" class="form-select border-2">
                                        <option value="M" <?= $admin['sexe'] == 'M' ? 'selected' : '' ?>>Homme</option>
                                        <option value="F" <?= $admin['sexe'] == 'F' ? 'selected' : '' ?>>Femme</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-5 d-flex gap-2">
                                <button type="submit" name="sauvegarder" class="btn btn-dark rounded-pill px-4 shadow">
                                    Enregistrer les changements
                                </button>
                                <a href="profil.php" class="btn btn-outline-secondary rounded-pill px-4">Annuler</a>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        
        <div class="mt-4 p-4 rounded-4 border bg-white shadow-sm d-flex justify-content-between align-items-center card">
            <div>
                <h6 class="fw-bold mb-1">Sécurité du compte</h6>
                <p class="text-muted small mb-0">Il est recommandé de changer votre mot de passe régulièrement.</p>
            </div>
            <a href="mot_de_passe.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                Changer le mot de passe
            </a>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>