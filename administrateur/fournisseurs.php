<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$message = "";
$mode_edition = false;
// Valeurs par défaut pour le formulaire
$f_form = ['id_fournisseur'=>'', 'nom'=>'', 'prenom'=>'', 'telephone'=>'', 'email'=>'', 'adresse'=>'', 'sexe'=>'M'];

// --- 1. LOGIQUE : SUPPRIMER ---
if (isset($_GET['del'])) {
    $id_del = (int)$_GET['del'];
    try {
        $stmt = $pdo->prepare("DELETE FROM fournisseurs WHERE id_fournisseur = ?");
        $stmt->execute([$id_del]);
        header("Location: fournisseurs.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger shadow-sm'>Erreur : Ce partenaire est lié à des produits et ne peut être supprimé.</div>";
    }
}

// --- 2. LOGIQUE : CHARGER POUR MODIFICATION ---
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE id_fournisseur = ?");
    $stmt->execute([$id_edit]);
    $res = $stmt->fetch();
    if($res) {
        $f_form = $res;
        $mode_edition = true;
    }
}

// --- 3. LOGIQUE : ENREGISTRER (AJOUT OU MODIF) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer_fourn'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $tel = htmlspecialchars($_POST['telephone']);
    $email = htmlspecialchars($_POST['email']);
    $adr = htmlspecialchars($_POST['adresse']);
    $sexe = htmlspecialchars($_POST['sexe']);
    $id_fourn = $_POST['id_fournisseur'];

    try {
        if (!empty($id_fourn)) {
            // MODE MODIFICATION
            $upd = $pdo->prepare("UPDATE fournisseurs SET nom=?, prenom=?, telephone=?, adresse=?, email=?, sexe=? WHERE id_fournisseur=?");
            $upd->execute([$nom, $prenom, $tel, $adr, $email, $sexe, $id_fourn]);
            $redirection = "updated";
        } else {
            // MODE AJOUT
            $ins = $pdo->prepare("INSERT INTO fournisseurs (nom, prenom, telephone, adresse, email, sexe) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->execute([$nom, $prenom, $tel, $adr, $email, $sexe]);
            $redirection = "added";
        }
        header("Location: fournisseurs.php?msg=$redirection");
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger shadow-sm'>Erreur : L'email est peut-être déjà utilisé.</div>";
    }
}

// --- 4. RÉCUPÉRER LA LISTE ---
$liste = $pdo->query("SELECT * FROM fournisseurs ORDER BY nom ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Fournisseurs - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=2.0">
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
    <a href="promotions.php"><i class="bi bi-megaphone me-2"></i> Promotions</a>
    <a href="fournisseurs.php" class="active"><i class="bi bi-truck me-2"></i> Fournisseurs</a>
    <a href="clients.php"><i class="bi bi-people me-2"></i> Clients</a>
    <a href="commandes.php"><i class="bi bi-cart-check me-2"></i> Commandes</a>
</div>

<div class="main-content">
    <div class="container-fluid py-4">
        <h2 class="fw-bold mb-4">Gestion des Partenaires</h2>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success border-0 shadow-sm">Nouveau partenaire ajouté !</div>
            <?php elseif($_GET['msg'] == 'updated'): ?>
                <div class="alert alert-primary border-0 shadow-sm">Informations mises à jour !</div>
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-info border-0 shadow-sm">Partenaire supprimé.</div>
            <?php endif; ?>
        <?php endif; ?>
        <?= $message ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 <?= $mode_edition ? 'border-top border-4 border-warning' : '' ?>">
                    <h5 class="fw-bold mb-3"><?= $mode_edition ? "Modifier : " . $f_form['nom'] : "Nouveau Partenaire" ?></h5>
                    <form method="POST">
                        <input type="hidden" name="id_fournisseur" value="<?= $f_form['id_fournisseur'] ?>">
                        
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="small fw-bold">Nom / Société</label>
                                <input type="text" name="nom" class="form-control bg-light border-0" value="<?= $f_form['nom'] ?>" required>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="small fw-bold">Prénom</label>
                                <input type="text" name="prenom" class="form-control bg-light border-0" value="<?= $f_form['prenom'] ?>">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="small fw-bold">Sexe</label>
                            <select name="sexe" class="form-select bg-light border-0">
                                <option value="M" <?= $f_form['sexe'] == 'M' ? 'selected' : '' ?>>Masculin (M)</option>
                                <option value="F" <?= $f_form['sexe'] == 'F' ? 'selected' : '' ?>>Féminin (F)</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="small fw-bold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control bg-light border-0" value="<?= $f_form['telephone'] ?>" placeholder="+269...">
                        </div>

                        <div class="mb-2">
                            <label class="small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0" value="<?= $f_form['email'] ?>">
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold">Adresse</label>
                            <textarea name="adresse" class="form-control bg-light border-0" rows="2"><?= $f_form['adresse'] ?></textarea>
                        </div>

                        <button type="submit" name="enregistrer_fourn" class="btn <?= $mode_edition ? 'btn-warning' : 'btn-dark' ?> w-100 rounded-pill fw-bold">
                            <?= $mode_edition ? "METTRE À JOUR" : "ENREGISTRER" ?>
                        </button>

                        <?php if($mode_edition): ?>
                            <a href="fournisseurs.php" class="btn btn-link w-100 text-muted mt-2 small text-decoration-none">Annuler la modification</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="ps-3 py-3">Partenaire</th>
                                <th>Contact / Localisation</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($liste as $f): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-<?= $f['sexe'] == 'M' ? 'primary' : 'danger' ?> text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px;">
                                            <?= strtoupper(substr($f['nom'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block"><?= strtoupper($f['nom']) ?> <?= $f['prenom'] ?></span>
                                            <span class="badge bg-light text-dark border small">ID: #<?= $f['id_fournisseur'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-telephone text-muted me-1"></i> <?= $f['telephone'] ?: 'N/A' ?><br>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= $f['adresse'] ?: 'Non renseignée' ?></small>
                                </td>
                                <td class="text-center">
                                    <a href="?edit=<?= $f['id_fournisseur'] ?>" class="btn btn-sm btn-outline-primary border-0 me-1 shadow-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="?del=<?= $f['id_fournisseur'] ?>" class="btn btn-sm btn-outline-danger border-0 shadow-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce contact ?')">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>