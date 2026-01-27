<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$message = "";

// 1. LOGIQUE : SUPPRIMER UNE PROMO (ET REMETTRE LE PRIX À NULL)
if (isset($_GET['del'])) {
    $id_del = (int)$_GET['del'];
    
    try {
        $pdo->beginTransaction();

        // On récupère l'id_produit avant de supprimer la promo pour nettoyer le prix
        $stmtGet = $pdo->prepare("SELECT id_produit FROM promotion WHERE id_promotion = ?");
        $stmtGet->execute([$id_del]);
        $promoInfo = $stmtGet->fetch();

        if ($promoInfo) {
            // On supprime la promotion
            $stmtDel = $pdo->prepare("DELETE FROM promotion WHERE id_promotion = ?");
            $stmtDel->execute([$id_del]);

            // On remet le prix_promo à NULL dans la table produit
            $stmtUpd = $pdo->prepare("UPDATE produit SET prix_promo = NULL WHERE id_produit = ?");
            $stmtUpd->execute([$promoInfo['id_produit']]);
        }

        $pdo->commit();
        header("Location: promotions.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur lors de la suppression.</div>";
    }
}

// 2. LOGIQUE : ENREGISTRER UNE PROMO (ET CALCULER LE PRIX_PROMO)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_promo'])) {
    $id_p = (int)$_POST['id_produit'];
    $code = htmlspecialchars($_POST['code_promo']);
    $pct  = (float)$_POST['pourcentage'];
    $debut = $_POST['date_debut'];
    $fin   = $_POST['date_fin'];

    try {
        $pdo->beginTransaction();

        // A. Insertion dans la table promotion
        $ins = $pdo->prepare("INSERT INTO promotion (id_produit, code_promo, pourcentage_reduction, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$id_p, $code, $pct, $debut, $fin]);

        // B. Calcul et mise à jour automatique dans la table produit
        $getProd = $pdo->prepare("SELECT prix_unitaire FROM produit WHERE id_produit = ?");
        $getProd->execute([$id_p]);
        $pInfo = $getProd->fetch();

        if ($pInfo) {
            $nouveau_prix = $pInfo['prix_unitaire'] * (1 - ($pct / 100));
            $upd = $pdo->prepare("UPDATE produit SET prix_promo = ? WHERE id_produit = ?");
            $upd->execute([$nouveau_prix, $id_p]);
        }

        $pdo->commit();
        header("Location: promotions.php?msg=added");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : Ce code existe déjà ou le produit est introuvable.</div>";
    }
}

// 3. RÉCUPÉRER LES DONNÉES
$produits = $pdo->query("SELECT id_produit, nom_produit, prix_unitaire FROM produit ORDER BY nom_produit")->fetchAll();

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
                    <h5 class="fw-bold mb-3 text-dark">Nouvelle Offre</h5>
                    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                        <div class="alert alert-success">Promotion ajoutée et prix mis à jour !</div>
                    <?php endif; ?>
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Produit à promotionner</label>
                            <select name="id_produit" class="form-select bg-light border-0" required>
                                <?php foreach($produits as $pr): ?>
                                    <option value="<?= $pr['id_produit'] ?>"><?= $pr['nom_produit'] ?> (<?= number_format($pr['prix_unitaire'], 0, ',', ' ') ?> FCFA)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Code Promo (ex: SOLDES25)</label>
                            <input type="text" name="code_promo" class="form-control bg-light border-0" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Réduction (%)</label>
                            <input type="number" name="pourcentage" class="form-control bg-light border-0" required min="1" max="100">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small fw-bold">Début</label>
                                <input type="date" name="date_debut" class="form-control bg-light border-0" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6 mb-4">
                                <label class="small fw-bold">Fin</label>
                                <input type="date" name="date_fin" class="form-control bg-light border-0" required>
                            </div>
                        </div>
                        <button type="submit" name="ajouter_promo" class="btn btn-dark w-100 rounded-pill fw-bold shadow-sm">ACTIVER LA PROMO</button>
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
                                            <img src="../<?= $p['image_produit'] ?>" width="45" height="45" class="object-fit-cover me-2 border">
                                            <div>
                                                <span class="fw-bold d-block"><?= $p['nom_produit'] ?></span>
                                                <small class="badge bg-light text-dark border">Code: <?= $p['code_promo'] ?></small>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Produit supprimé !</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-danger fs-5">-<?= (int)$p['pourcentage_reduction'] ?>%</td>
                                <td class="text-center small">
                                    <span class="d-block fw-bold"><?= date('d/m/Y', strtotime($p['date_fin'])) ?></span>
                                    <span class="badge <?= $est_expire ? 'bg-secondary' : 'bg-success' ?> rounded-pill">
                                        <?= $est_expire ? 'Expirée' : 'Active' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?del=<?= $p['id_promotion'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Supprimer cette promotion et rétablir le prix normal ?')">
                                        <i class="bi bi-trash3-fill fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($promos_actives)): ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Aucune promotion en cours.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>