<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

$message = "";

// --- 1. LOGIQUE : SUPPRIMER UNE PROMO ---
if (isset($_GET['del'])) {
    $id_del = (int)$_GET['del'];
    try {
        $pdo->beginTransaction();
        $stmtGet = $pdo->prepare("SELECT id_produit FROM promotion WHERE id_promotion = ?");
        $stmtGet->execute([$id_del]);
        $promoInfo = $stmtGet->fetch();

        if ($promoInfo) {
            $stmtDel = $pdo->prepare("DELETE FROM promotion WHERE id_promotion = ?");
            $stmtDel->execute([$id_del]);
            $stmtUpd = $pdo->prepare("UPDATE produit SET prix_promo = NULL WHERE id_produit = ?");
            $stmtUpd->execute([$promoInfo['id_produit']]);
        }
        $pdo->commit();
        header("Location: promotions.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger text-center'>❌ Erreur lors de la suppression.</div>";
    }
}

// --- 2. LOGIQUE : ENREGISTRER UNE NOUVELLE PROMO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_promo'])) {
    $id_p  = (int)$_POST['id_produit'];
    $code  = htmlspecialchars(strtoupper($_POST['code_promo']));
    $pct   = (float)$_POST['pourcentage'];
    $debut = $_POST['date_debut'];
    $fin   = $_POST['date_fin'];

    try {
        $pdo->beginTransaction();
        $ins = $pdo->prepare("INSERT INTO promotion (id_produit, code_promo, pourcentage_reduction, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$id_p, $code, $pct, $debut, $fin]);

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
        $message = "<div class='alert alert-danger text-center'>❌ Erreur : Ce code existe déjà ou produit introuvable.</div>";
    }
}

// --- 3. LOGIQUE : MODIFIER UNE PROMO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_promo'])) {
    $id_promo = (int)$_POST['id_promotion'];
    $pct      = (float)$_POST['pourcentage'];
    $debut    = $_POST['date_debut'];
    $fin      = $_POST['date_fin'];

    try {
        $pdo->beginTransaction();
        $updPromo = $pdo->prepare("UPDATE promotion SET pourcentage_reduction = ?, date_debut = ?, date_fin = ? WHERE id_promotion = ?");
        $updPromo->execute([$pct, $debut, $fin, $id_promo]);

        $stmtGet = $pdo->prepare("SELECT id_produit FROM promotion WHERE id_promotion = ?");
        $stmtGet->execute([$id_promo]);
        $id_p = $stmtGet->fetchColumn();

        $getProd = $pdo->prepare("SELECT prix_unitaire FROM produit WHERE id_produit = ?");
        $getProd->execute([$id_p]);
        $prix_base = $getProd->fetchColumn();

        $nouveau_prix = $prix_base * (1 - ($pct / 100));
        $updProd = $pdo->prepare("UPDATE produit SET prix_promo = ? WHERE id_produit = ?");
        $updProd->execute([$nouveau_prix, $id_p]);

        $pdo->commit();
        header("Location: promotions.php?msg=updated");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger text-center'>❌ Erreur lors de la modification.</div>";
    }
}

// --- RÉCUPÉRATION DES DONNÉES ---
$produits = $pdo->query("SELECT id_produit, nom_produit, prix_unitaire FROM produit ORDER BY nom_produit")->fetchAll();
$promos_actives = $pdo->query("SELECT pr.*, p.nom_produit, p.prix_unitaire, p.image_produit FROM promotion pr LEFT JOIN produit p ON pr.id_produit = p.id_produit ORDER BY pr.id_promotion DESC")->fetchAll();

include 'header_admin.php'; 
?>

<div class="container-fluid py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-1">Gestion des Promotions</h2>
        <p class="text-muted">Visualisez et modifiez vos offres en temps réel</p>
        <div class="mx-auto" style="width: 60px; height: 4px; background: #000; border-radius: 10px;"></div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 text-center">
            <?= $_GET['msg'] == 'added' ? '✅ Promotion activée !' : ($_GET['msg'] == 'updated' ? '🔄 Mise à jour réussie !' : '🗑️ Promotion supprimée !') ?>
        </div>
    <?php endif; ?>
    <?= $message ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-4 rounded-4">
                <h5 class="fw-bold mb-4 text-uppercase small">Nouvelle Offre</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">PRODUIT CIBLE</label>
                        <select name="id_produit" class="form-select border-2" required>
                            <option value="">-- Choisir un article --</option>
                            <?php foreach($produits as $pr): ?>
                                <option value="<?= $pr['id_produit'] ?>"><?= htmlspecialchars($pr['nom_produit']) ?> (<?= number_format($pr['prix_unitaire'], 0, ',', ' ') ?> F)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">CODE PROMO</label>
                        <input type="text" name="code_promo" class="form-control border-2" placeholder="EX: AFRO20" required style="text-transform: uppercase;">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">RÉDUCTION (%)</label>
                        <input type="number" name="pourcentage" class="form-control border-2" required min="1" max="99">
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">DÉBUT</label>
                            <input type="date" name="date_debut" class="form-control border-2" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">FIN</label>
                            <input type="date" name="date_fin" class="form-control border-2" required>
                        </div>
                    </div>
                    <button type="submit" name="ajouter_promo" class="btn btn-dark w-100 rounded-pill fw-bold py-2 shadow-sm">ACTIVER LA PROMO</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="ps-3 py-3 text-start">Produit & Code</th>
                                <th>Remise</th>
                                <th>Validité</th>
                                <th class="pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($promos_actives as $p): 
                                $debut = new DateTime($p['date_debut']);
                                $fin = new DateTime($p['date_fin']);
                                $aujourdhui = new DateTime();
                                
                                $est_expire = ($aujourdhui > $fin);
                                $pas_encore_commence = ($aujourdhui < $debut);

                                if ($est_expire) { 
                                    $st_class = "bg-secondary"; $st_lbl = "EXPIRÉE"; 
                                    $row_style = "background-color: #f8f9fa; color: #6c757d; border-left: 5px solid #dee2e6;";
                                } elseif ($pas_encore_commence) { 
                                    $st_class = "bg-info text-dark"; $st_lbl = "À VENIR"; 
                                    $row_style = "border-left: 5px solid #0dcaf0;";
                                } else { 
                                    $st_class = "bg-success"; $st_lbl = "EN COURS"; 
                                    $row_style = "border-left: 5px solid #198754;";
                                }
                            ?>
                            <tr style="<?= $row_style ?>">
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="../<?= $p['image_produit'] ?>" width="50" height="50" 
                                             class="rounded-3 border me-3 object-fit-cover shadow-sm" 
                                             style="<?= $est_expire ? 'filter: grayscale(100%); opacity: 0.6;' : '' ?>">
                                        <div>
                                            <span class="fw-bold d-block small"><?= htmlspecialchars($p['nom_produit'] ?? 'Article supprimé') ?></span>
                                            <span class="badge bg-light text-dark border mt-1" style="font-size: 0.65rem;">CODE: <?= htmlspecialchars($p['code_promo']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-bold <?= $est_expire ? 'text-muted' : 'text-danger' ?>" style="font-size: 1.1rem;">
                                    -<?= (int)$p['pourcentage_reduction'] ?>%
                                </td>
                                <td class="text-center">
                                    <div class="small mb-1">Du <strong><?= date('d/m/Y', strtotime($p['date_debut'])) ?></strong></div>
                                    <div class="small mb-2">Au <strong><?= date('d/m/Y', strtotime($p['date_fin'])) ?></strong></div>
                                    <span class="badge <?= $st_class ?> rounded-pill" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                        <?= $st_lbl ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm <?= $est_expire ? 'btn-outline-secondary' : 'btn-outline-primary' ?> rounded-circle" 
                                                data-bs-toggle="modal" data-bs-target="#edit<?= $p['id_promotion'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?del=<?= $p['id_promotion'] ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-circle" 
                                           onclick="return confirm('Supprimer cette promo ?')">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit<?= $p['id_promotion'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0">
                                            <h5 class="fw-bold mb-0">Modifier la promo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body pt-0">
                                                <input type="hidden" name="id_promotion" value="<?= $p['id_promotion'] ?>">
                                                <p class="text-muted small mb-4">Produit : <?= htmlspecialchars($p['nom_produit']) ?></p>
                                                
                                                <div class="mb-3">
                                                    <label class="small fw-bold text-muted">RÉDUCTION (%)</label>
                                                    <input type="number" name="pourcentage" class="form-control border-2" value="<?= (int)$p['pourcentage_reduction'] ?>" required min="1" max="99">
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <label class="small fw-bold text-muted">DATE DÉBUT</label>
                                                        <input type="date" name="date_debut" class="form-control border-2" value="<?= $p['date_debut'] ?>" required>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="small fw-bold text-muted">DATE FIN</label>
                                                        <input type="date" name="date_fin" class="form-control border-2" value="<?= $p['date_fin'] ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-3 text-center">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" name="modifier_promo" class="btn btn-dark rounded-pill px-4">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <?php if(empty($promos_actives)): ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Aucune promotion pour le moment.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>