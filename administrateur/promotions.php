<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$message = "";

// 1. LOGIQUE : SUPPRIMER UNE PROMO (CRUCIAL POUR LA SYNCHRO)
if (isset($_GET['del'])) {
    $id_del = (int)$_GET['del'];
    $stmt = $pdo->prepare("DELETE FROM promotion WHERE id_promotion = ?");
    if ($stmt->execute([$id_del])) {
        header("Location: promotions.php?msg=deleted");
        exit();
    }
}

// 2. LOGIQUE : ENREGISTRER UNE PROMO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_promo'])) {
    $id_p = (int)$_POST['id_produit'];
    $code = htmlspecialchars($_POST['code_promo']);
    $pct  = (float)$_POST['pourcentage'];
    $debut = $_POST['date_debut'];
    $fin   = $_POST['date_fin'];

    try {
        $ins = $pdo->prepare("INSERT INTO promotion (id_produit, code_promo, pourcentage_reduction, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$id_p, $code, $pct, $debut, $fin]);
        header("Location: promotions.php?msg=added");
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Erreur : Ce code existe déjà.</div>";
    }
}

// 3. RÉCUPÉRER LES DONNÉES (VERSION LEFT JOIN POUR TOUT SYNCHRONISER)
$produits = $pdo->query("SELECT id_produit, nom_produit, prix_unitaire FROM produit ORDER BY nom_produit")->fetchAll();

// On utilise LEFT JOIN pour être sûr de voir les 6 promos, même si le produit n'existe plus
$promos_actives = $pdo->query("SELECT pr.*, p.nom_produit, p.prix_unitaire, p.image_produit 
                               FROM promotion pr 
                               LEFT JOIN produit p ON pr.id_produit = p.id_produit 
                               ORDER BY pr.id_promotion DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Promotions - AfroStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/css/styleadmin.css?v=1.9">
    <style>
        .promo-expired { opacity: 0.6; filter: grayscale(1); }
        .object-fit-cover { object-fit: cover; border-radius: 8px; }
    </style>
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

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 p-4 rounded-4">
                    <h5 class="fw-bold mb-3">Nouvelle Offre</h5>
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Produit</label>
                            <select name="id_produit" class="form-select bg-light border-0" required>
                                <?php foreach($produits as $pr): ?>
                                    <option value="<?= $pr['id_produit'] ?>"><?= $pr['nom_produit'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Code Promo</label>
                            <input type="text" name="code_promo" class="form-control bg-light border-0" placeholder="EX: AFRO20" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Réduction (%)</label>
                            <input type="number" name="pourcentage" class="form-control bg-light border-0" required min="1" max="100">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Début</label>
                            <input type="date" name="date_debut" class="form-control bg-light border-0" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold">Fin</label>
                            <input type="date" name="date_fin" class="form-control bg-light border-0" required>
                        </div>
                        <button type="submit" name="ajouter_promo" class="btn btn-dark w-100 rounded-pill fw-bold">ENREGISTRER</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="ps-3 py-3">Produit</th>
                                <th class="text-center">Remise</th>
                                <th class="text-center">Validité</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($promos_actives as $p): 
                                $est_expire = (new DateTime() > new DateTime($p['date_fin']));
                            ?>
                            <tr class="<?= $est_expire ? 'promo-expired bg-light' : '' ?>">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <?php if($p['nom_produit']): ?>
                                            <img src="../<?= $p['image_produit'] ?>" width="45" height="45" class="object-fit-cover me-2">
                                            <div>
                                                <span class="fw-bold d-block"><?= $p['nom_produit'] ?></span>
                                                <small class="text-muted">CODE: <?= $p['code_promo'] ?></small>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Produit supprimé !</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-danger">-<?= (int)$p['pourcentage_reduction'] ?>%</td>
                                <td class="text-center small">
                                    <?= date('d/m/Y', strtotime($p['date_fin'])) ?><br>
                                    <span class="badge <?= $est_expire ? 'bg-secondary' : 'bg-success' ?> rounded-pill">
                                        <?= $est_expire ? 'Expirée' : 'Active' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?del=<?= $p['id_promotion'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Supprimer définitivement ?')">
                                        <i class="bi bi-trash3-fill"></i>
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

</body>
</html>