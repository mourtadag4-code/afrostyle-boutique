<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$message = "";

// 1. LOGIQUE : ENREGISTRER DANS LA TABLE PROMOTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_promo'])) {
    $id_p = (int)$_POST['id_produit'];
    $code = htmlspecialchars($_POST['code_promo']);
    $pct  = (float)$_POST['pourcentage'];
    $debut = $_POST['date_debut'];
    $fin   = $_POST['date_fin'];

    try {
        $ins = $pdo->prepare("INSERT INTO promotion (id_produit, code_promo, pourcentage_reduction, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$id_p, $code, $pct, $debut, $fin]);
        $message = "<div class='alert alert-success'>Promotion enregistrée en base !</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Erreur : Vérifiez si le code existe déjà.</div>";
    }
}

// 2. LOGIQUE : SUPPRIMER UNE PROMO
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM promotion WHERE id_promotion = ?")->execute([(int)$_GET['del']]);
    header("Location: promotions.php");
    exit();
}

// 3. RÉCUPÉRER LES DONNÉES
$produits = $pdo->query("SELECT id_produit, nom_produit, prix_unitaire FROM produit ORDER BY nom_produit")->fetchAll();
$promos_actives = $pdo->query("SELECT pr.*, p.nom_produit, p.prix_unitaire, p.image_produit 
                               FROM promotion pr 
                               JOIN produit p ON pr.id_produit = p.id_produit")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Promotions - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.9">
</head>
<body>

<div class="sidebar">
    <div class="logo-area text-center py-4"><h4 style="color: #D4AF37;">AFROSTYLE</h4></div>
    <div class="mt-3">
        <a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="produits.php"><i class="bi bi-box-seam me-2"></i> Produits</a>
        <a href="promotions.php" class="active"><i class="bi bi-megaphone me-2"></i> Promotions</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid py-4">
        <h2 class="fw-bold mb-4">Gestion des Promotions</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4 rounded-4 mb-4">
                    <h5 class="fw-bold mb-3">Nouvelle Offre</h5>
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Produit</label>
                            <select name="id_produit" class="form-select border-0 bg-light" required>
                                <?php foreach($produits as $pr): ?>
                                    <option value="<?= $pr['id_produit'] ?>"><?= $pr['nom_produit'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Code Promo</label>
                            <input type="text" name="code_promo" class="form-control border-0 bg-light" placeholder="EX: VENTE20" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Réduction (%)</label>
                            <input type="number" name="pourcentage" class="form-control border-0 bg-light" placeholder="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Date Début</label>
                            <input type="date" name="date_debut" class="form-control border-0 bg-light" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Date Fin</label>
                            <input type="date" name="date_fin" class="form-control border-0 bg-light" required>
                        </div>
                        <button type="submit" name="ajouter_promo" class="btn btn-dark w-100 rounded-pill">ENREGISTRER</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="admin-table-card shadow-sm bg-white rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Produit</th>
                                <th class="text-center">Réduction</th>
                                <th class="text-center">Dates</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($promos_actives)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Aucune promo en base.</td></tr>
                            <?php endif; ?>
                            <?php foreach($promos_actives as $p): ?>
                            <tr>
                                <td class="ps-3">
                                    <strong><?= $p['nom_produit'] ?></strong><br>
                                    <code class="small"><?= $p['code_promo'] ?></code>
                                </td>
                                <td class="text-center"><span class="badge bg-danger">-<?= (int)$p['pourcentage_reduction'] ?>%</span></td>
                                <td class="text-center small">
                                    Du <?= date('d/m', strtotime($p['date_debut'])) ?><br>
                                    Au <?= date('d/m/y', strtotime($p['date_fin'])) ?>
                                </td>
                                <td class="text-center">
                                    <a href="?del=<?= $p['id_promotion'] ?>" class="text-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
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

</body>
</html>