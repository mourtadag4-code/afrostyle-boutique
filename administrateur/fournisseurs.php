<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$message = "";
$mode_edition = false;
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
            $upd = $pdo->prepare("UPDATE fournisseurs SET nom=?, prenom=?, telephone=?, adresse=?, email=?, sexe=? WHERE id_fournisseur=?");
            $upd->execute([$nom, $prenom, $tel, $adr, $email, $sexe, $id_fourn]);
            $redirection = "updated";
        } else {
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

// APPEL DU HEADER
include 'header_admin.php'; 
?>

<div class="container-fluid py-2">
    <h2 class="fw-bold mb-4">Gestion des Partenaires</h2>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-<?= ($_GET['msg']=='deleted') ? 'info' : 'success' ?> border-0 shadow-sm mb-4">
            <?php
                if($_GET['msg'] == 'added') echo "Nouveau partenaire ajouté !";
                if($_GET['msg'] == 'updated') echo "Informations mises à jour !";
                if($_GET['msg'] == 'deleted') echo "Partenaire supprimé.";
            ?>
        </div>
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
                        <a href="fournisseurs.php" class="btn btn-link w-100 text-muted mt-2 small text-decoration-none">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-3 py-3">Partenaire</th>
                            <th>Contact</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($liste as $f): ?>
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-<?= $f['sexe'] == 'M' ? 'primary' : 'danger' ?> text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px; font-weight:bold;">
                                        <?= strtoupper(substr($f['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark-emphasis"><?= strtoupper($f['nom']) ?> <?= $f['prenom'] ?></span>
                                        <span class="text-muted small">ID: #<?= $f['id_fournisseur'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-telephone me-1"></i> <?= $f['telephone'] ?: 'N/A' ?></div>
                                <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= $f['adresse'] ?: 'N/A' ?></div>
                            </td>
                            <td class="text-center">
                                <a href="?edit=<?= $f['id_fournisseur'] ?>" class="btn btn-sm btn-outline-primary border-0 shadow-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="?del=<?= $f['id_fournisseur'] ?>" class="btn btn-sm btn-outline-danger border-0 shadow-sm" onclick="return confirm('Supprimer ce contact ?')">
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

<?php include 'footer_admin.php'; ?>