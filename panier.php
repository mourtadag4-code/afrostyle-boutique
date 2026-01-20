<?php
session_start();
require_once "commun/connexiondb.php";

// --- 1. LOGIQUE DE TRAITEMENT (PHP) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $today = date('Y-m-d');

    if ($_GET['action'] == 'add') {
        $qte_ajoutee = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;
        if ($qte_ajoutee < 1) $qte_ajoutee = 1;

        $stmt = $pdo->prepare("
            SELECT p.*, prom.pourcentage_reduction 
            FROM produit p 
            LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
            AND ? BETWEEN prom.date_debut AND prom.date_fin 
            WHERE p.id_produit = ?");
        $stmt->execute([$today, $id]);
        $p = $stmt->fetch();

        if ($p) {
            $prix_de_base = (float)$p['prix_unitaire'];
            $reduction = isset($p['pourcentage_reduction']) ? (float)$p['pourcentage_reduction'] : 0;
            $prix_final = $prix_de_base * (1 - $reduction / 100);

            if (!isset($_SESSION['panier'][$id])) {
                $_SESSION['panier'][$id] = [
                    'nom' => $p['nom_produit'],
                    'prix' => $prix_final, 
                    'image' => $p['image_produit'],
                    'quantite' => $qte_ajoutee
                ];
            } else {
                $_SESSION['panier'][$id]['quantite'] += $qte_ajoutee;
            }
            $_SESSION['message'] = "Produit ajouté avec succès !";
            $_SESSION['msg_type'] = "success";
        }
    }

    if ($_GET['action'] == 'plus' && isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]['quantite']++;
    }
    
    if ($_GET['action'] == 'moins' && isset($_SESSION['panier'][$id])) {
        if ($_SESSION['panier'][$id]['quantite'] > 1) {
            $_SESSION['panier'][$id]['quantite']--;
        } else {
            unset($_SESSION['panier'][$id]);
        }
    }

    if ($_GET['action'] == 'delete' && isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }

    header("Location: panier.php");
    exit;
}

$page_title = "Mon Panier - AFROSTYLE";
require_once "commun/header.php";
?>

<style>
    :root { --afro-gold: #D4AF37; --afro-orange: #E97451; }
    body { background-color: #fdfdfd; font-family: 'Poppins', sans-serif; }
    
    .hero-header-cart { 
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; 
        color: white; padding: 60px 0; 
    }
    .cart-card {
        background: white; border: 1px solid #eee; border-radius: 20px;
        overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.03);
    }
    .table thead { background: #f8f9fa; border-bottom: 2px solid var(--afro-gold); }
    .btn-checkout {
        background: var(--afro-gold); border: none; color: white;
        padding: 15px; border-radius: 50px; font-weight: bold; transition: 0.3s;
    }
    .btn-checkout:hover { background: #b8962d; transform: scale(1.02); color: white; }
    .img-cart { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; border: 1px solid #eee; }
</style>

<div class="hero-header-cart text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold text-uppercase">Mon Panier</h1>
        <p class="lead">Prêt à porter vos plus belles tenues AfroStyle ?</p>
    </div>
</div>

<div class="container mb-5">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type']; ?> alert-dismissible fade show shadow-sm border-0 mb-4 rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
    <?php endif; ?>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <i class="bi bi-cart-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            <h4 class="mt-3 text-uppercase fw-bold">Votre panier est vide</h4>
            <p class="text-muted">Vos futurs coups de cœur vous attendent dans le catalogue.</p>
            <a href="catalogue.php" class="btn btn-warning mt-3 px-5 rounded-pill fw-bold text-white shadow">Découvrir nos modèles</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-uppercase small fw-bold">
                                    <th class="ps-4 py-3">Produit</th>
                                    <th>Prix</th>
                                    <th>Quantité</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_general = 0;
                                foreach ($_SESSION['panier'] as $id => $item): 
                                    $sous_total = $item['prix'] * $item['quantite'];
                                    $total_general += $sous_total;
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($item['image']) ?>" class="img-cart me-3">
                                            <div>
                                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($item['nom']) ?></h6>
                                                <a href="?action=delete&id=<?= $id ?>" class="text-danger small text-decoration-none">
                                                    <i class="bi bi-trash me-1"></i>Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold"><?= number_format($item['prix'], 0, '', ' ') ?> FCFA</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="?action=moins&id=<?= $id ?>" class="btn btn-sm btn-outline-dark rounded-circle px-2">-</a>
                                            <span class="fw-bold px-1"><?= $item['quantite'] ?></span>
                                            <a href="?action=plus&id=<?= $id ?>" class="btn btn-sm btn-outline-dark rounded-circle px-2">+</a>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-warning">
                                        <?= number_format($sous_total, 0, '', ' ') ?> FCFA
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-card p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-4 text-uppercase border-bottom pb-2">Résumé</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Articles :</span>
                        <span class="fw-bold"><?= array_sum(array_column($_SESSION['panier'], 'quantite')) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Livraison :</span>
                        <span class="text-success fw-bold">OFFERTE</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 border-top pt-3">
                        <span class="h5 fw-bold">TOTAL :</span>
                        <span class="h5 fw-bold text-danger"><?= number_format($total_general, 0, '', ' ') ?> FCFA</span>
                    </div>

                    <?php $_SESSION['total_a_payer'] = $total_general; ?>

                    <a href="confirmation.php" class="btn btn-checkout w-100 mb-3 text-uppercase">
                        Valider ma commande
                    </a>
                    <a href="catalogue.php" class="btn btn-link w-100 text-muted text-decoration-none small">
                        ← Continuer mes achats
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once "commun/footer.php"; ?>