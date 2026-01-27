<?php
session_start();
require_once "commun/connexiondb.php";

$id_user = $_SESSION['utilisateur_id'] ?? null;

// --- AJOUT/SUPPRESSION ---
if (isset($_GET['add'])) {
    $id_add = (int)$_GET['add'];
    if (!isset($_SESSION['favoris'])) { $_SESSION['favoris'] = []; }
    $_SESSION['favoris'][$id_add] = true;
    if ($id_user) { 
        $stmt = $pdo->prepare("INSERT IGNORE INTO favoris (id_utilisateur, id_produit) VALUES (?, ?)");
        $stmt->execute([$id_user, $id_add]); 
    }
    header("Location: catalogue.php"); exit;
}

if (isset($_GET['remove'])) {
    $id_rem = (int)$_GET['remove'];
    if (isset($_SESSION['favoris'][$id_rem])) { unset($_SESSION['favoris'][$id_rem]); }
    if ($id_user) { 
        $stmt = $pdo->prepare("DELETE FROM favoris WHERE id_utilisateur = ? AND id_produit = ?");
        $stmt->execute([$id_user, $id_rem]); 
    }
    header("Location: " . ((isset($_GET['from']) && $_GET['from'] == 'cat') ? "catalogue.php" : "favoris.php")); exit;
}

// --- CONFIG PAGINATION ---
$fav_ids = isset($_SESSION['favoris']) ? array_keys($_SESSION['favoris']) : [];
$parPageFav = 12; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalFavs = count($fav_ids);
$totalPages = ceil($totalFavs / $parPageFav);
$offset = ($page - 1) * $parPageFav;

$page_title = "Mes Favoris - AfroStyle";
include_once "commun/header.php";
?>

<style>
    :root { 
        --afro-gold: #D4AF37; 
        --promo-rouge: #e63946;
        --fond-photo: #f5f2eb;
    }
    .hero-header { 
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; 
        color: white; padding: 70px 0; 
    }
    .fav-card { 
        background: white; border-radius: 20px; border-bottom: 5px solid var(--afro-gold); 
        overflow: hidden; height: 100%; transition: 0.3s; display: flex; flex-direction: column; 
    }
    .fav-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0,0,0,0.1); }
    
    .img-wrapper { height: 250px; background: var(--fond-photo); display: flex; align-items: center; justify-content: center; position: relative; }
    .img-wrapper img { max-width: 80%; max-height: 80%; object-fit: contain; }
    
    .btn-delete { 
        position: absolute; top: 10px; right: 10px; color: var(--promo-rouge); 
        background: white; border-radius: 50%; width: 35px; height: 35px; 
        display: flex; align-items: center; justify-content: center; text-decoration: none; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 5;
    }
    
    .promo-tag { 
        position: absolute; top: 10px; left: 10px; background: var(--promo-rouge); 
        color: white; font-size: 0.7rem; padding: 4px 10px; border-radius: 50px; font-weight: bold; 
    }

    /* PRIX BARRÉ */
    .price-new { color: var(--afro-gold); font-weight: 800; font-size: 1.1rem; }
    .price-old { text-decoration: line-through; color: var(--promo-rouge); font-size: 0.85rem; margin-right: 8px; opacity: 0.8; }

    .btn-decouvrir { background-color: var(--afro-gold); color: white; border: none; transition: 0.3s; }
    .btn-decouvrir:hover { background-color: #b8962d; color: white; }
    
    .pagination .page-item.active .page-link { background: var(--afro-gold); border-color: var(--afro-gold); }
</style>

<div class="hero-header text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold text-uppercase">Mes Favoris</h1>
        <p class="lead">Un coup d'œil sur les produits qui vous ont fait craquer !</p>
    </div>
</div>

<div class="container mb-5">
    <?php if (empty($fav_ids)): ?>
        <div class="text-center py-5">
            <h4 class="text-muted">Votre liste de favoris est vide.</h4>
            <a href="catalogue.php" class="btn btn-dark mt-3 rounded-pill fw-bold px-4 shadow-sm">Découvrir le catalogue</a>
        </div>
    <?php else: 
        $ids_page = array_slice($fav_ids, $offset, $parPageFav);
        $placeholders = implode(',', array_fill(0, count($ids_page), '?'));
        $today = date('Y-m-d');
        
        $stmt = $pdo->prepare("SELECT p.*, prom.pourcentage_reduction 
                               FROM produit p 
                               LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
                               AND ? BETWEEN prom.date_debut AND prom.date_fin
                               WHERE p.id_produit IN ($placeholders)");
        $stmt->execute(array_merge([$today], $ids_page));
        $produits = $stmt->fetchAll();
    ?>
        <div class="row g-4">
            <?php foreach ($produits as $row): 
                $reduc = $row['pourcentage_reduction'] ?? 0;
                $prix_final = $row['prix_unitaire'] * (1 - $reduc/100);
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="fav-card shadow-sm">
                        <div class="img-wrapper">
                            <a href="favoris.php?remove=<?= $row['id_produit'] ?>" class="btn-delete" title="Retirer des favoris">
                                <i class="bi bi-heart-fill"></i>
                            </a>
                            
                            <?php if ($reduc > 0): ?>
                                <div class="promo-tag">-<?= (int)$reduc ?>%</div>
                            <?php endif; ?>
                            
                            <img src="<?= htmlspecialchars($row['image_produit']) ?>" onerror="this.src='public/Images/placeholder.png'">
                        </div>

                        <div class="p-3 text-center d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold text-truncate mb-2 small"><?= htmlspecialchars($row['nom_produit']) ?></h6>
                            
                            <div class="mb-3">
                                <?php if ($reduc > 0): ?>
                                    <span class="price-old"><?= number_format($row['prix_unitaire'], 0, '', ' ') ?></span>
                                <?php endif; ?>
                                <span class="price-new"><?= number_format($prix_final, 0, '', ' ') ?> FCFA</span>
                            </div>

                            <div class="d-grid gap-2 mt-auto">
                                <a href="panier.php?action=add&id=<?= $row['id_produit'] ?>" class="btn btn-dark btn-sm rounded-pill fw-bold">
                                    <i class="bi bi-cart-plus me-1"></i> PANIER
                                </a>
                                <a href="produit_detail.php?id=<?= $row['id_produit'] ?>" class="btn btn-decouvrir btn-sm rounded-pill fw-bold">
                                    DÉCOUVRIR
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if($totalPages > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link shadow-sm border-0 mx-1 rounded-circle" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include_once "commun/footer.php"; ?>