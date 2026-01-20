<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$id_admin = $_SESSION['user_id'];
$message = "";
$mode_edition = isset($_GET['edit']); // On vérifie si on est en mode édition

// --- 1. TRAITEMENT DE LA MISE À JOUR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sauvegarder'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $sexe = $_POST['sexe'];

    $sql = "UPDATE utilisateurs SET nom=?, prenom=?, adresse=?, telephone=?, sexe=? WHERE id_utilisateur=?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nom, $prenom, $adresse, $telephone, $sexe, $id_admin])) {
        $_SESSION['user_nom'] = $nom;
        $message = "<div class='alert alert-success rounded-pill text-center'>✅ Modifications enregistrées !</div>";
        $mode_edition = false; // On repasse en mode affichage
    }
}

// --- 2. RÉCUPÉRATION DES DONNÉES ---
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id_admin]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.3">
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <h4 class="text-white fw-bold">AFROSTYLE</h4>
        <hr class="text-white opacity-25">
    </div>
    <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="profil.php" class="active"><i class="bi bi-person me-2"></i> Mon Profil</a>
    <a href="produits.php"><i class="bi bi-box-seam me-2"></i> Produits</a>
    <a href="deconnexion.php" class="text-danger mt-5"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
</div>

<div class="main-content">
    <h2 class="fw-bold">Gestion du Profil</h2>

    <div class="container mt-4" style="max-width: 900px;">
        <?= $message ?>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="row g-0">
                <div class="col-md-4 bg-dark text-center p-5 text-white">
                    <div class="bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto shadow" style="width:100px; height:100px; font-size: 2.5rem;">
                        <?= strtoupper(substr($admin['nom'], 0, 1)) ?>
                    </div>
                    <h4 class="fw-bold"><?= htmlspecialchars($admin['nom']) ?></h4>
                    <span class="badge bg-warning text-dark rounded-pill"><?= strtoupper($admin['role']) ?></span>
                </div>

                <div class="col-md-8 bg-white p-5">
                    
                    <?php if (!$mode_edition): ?>
                        <h5 class="fw-bold mb-4">Mes Informations</h5>
                        <p><strong>Prénom :</strong> <?= htmlspecialchars($admin['prenom']) ?></p>
                        <p><strong>Email :</strong> <?= htmlspecialchars($admin['email']) ?></p>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($admin['telephone'] ?: 'Non renseigné') ?></p>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($admin['adresse'] ?: 'Non renseignée') ?></p>
                        <p><strong>Sexe :</strong> <?= $admin['sexe'] == 'F' ? 'Femme' : 'Homme' ?></p>
                        <hr>
                        <a href="profil.php?edit=1" class="btn btn-dark rounded-pill px-4">Modifier mon profil</a>

                    <?php else: ?>
                        <h5 class="fw-bold mb-4 text-warning">Modifier mes informations</h5>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Nom</label>
                                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($admin['nom']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Prénom</label>
                                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($admin['prenom']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold">Adresse</label>
                                    <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($admin['adresse']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Téléphone</label>
                                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($admin['telephone']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Sexe</label>
                                    <select name="sexe" class="form-select">
                                        <option value="M" <?= $admin['sexe'] == 'M' ? 'selected' : '' ?>>Homme</option>
                                        <option value="F" <?= $admin['sexe'] == 'F' ? 'selected' : '' ?>>Femme</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" name="sauvegarder" class="btn btn-success rounded-pill px-4">Enregistrer</button>
                                <a href="profil.php" class="btn btn-outline-secondary rounded-pill px-4">Annuler</a>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>