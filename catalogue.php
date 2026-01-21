<?php
session_start();
require_once 'commun/connexiondb.php';

// --- FILTRES ET TRI ---
$cat_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$recherche = $_GET['q'] ?? '';
$tri = $_GET['tri'] ?? 'new'; 
$today = date('Y-m-d');

// 1. Construction de la requête SQL avec jointure Promotion
$sql = "SELECT p.*, prom.pourcentage_reduction 
        FROM produit p 
        LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
        AND ? BETWEEN prom.date_debut AND prom.date_fin 
        WHERE 1=1";

if ($cat_id > 0) $sql .= " AND p.id_categorie = $cat_id";
if ($recherche) $sql .= " AND p.nom_produit LIKE " . $pdo->quote('%'.$recherche.'%');

// Logique de tri
switch ($tri) {
    case 'prix_asc': $sql .= " ORDER BY p.prix_unitaire ASC"; break;
    case 'prix_desc': $sql .= " ORDER BY p.prix_unitaire DESC"; break;
    case 'new': 
    default: $sql .= " ORDER BY p.id_produit DESC"; break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$today]);
$produits = $stmt->fetchAll();

$page_title = "Catalogue - AfroStyle";
include_once "commun/header.php";
?>

<style>
    :root { --or-afro: #D4AF37; --or-fonce: #B8860B; --promo-rouge: #e63946; }
    
    .page-header { 
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; 
        color: white; padding: 60px 0; margin-bottom: 40px;
    }

    .sidebar-filter { border: none; border-radius: 15px; background: #fff; }
    .cat-link { 
        display: block; padding: 12px 15px; color: #333; text-decoration: none; 
        font-weight: 600; border-bottom: 1px solid #f8f4eb; transition: 0.3s;
    }
    .cat-link:hover, .cat-link.active { color: var(--or-afro); background: #fdfaf3; padding-left: 25px; }

    .product-card { 
        border: none; border-radius: 15px; background: #fff; transition: 0.3s; 
        position: relative; overflow: hidden; height: 100%; display: flex; flex-direction: column;
    }
    .product-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    
    .img-box { height: 230px; background: #fdfaf3; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
    .img-box img { max-height: 85%; max-width: 90%; transition: 0.4s; object-fit: contain; }

    .btn-fav { 
        position: absolute; top: 10px; right: 10px; background: white; 
        width: 35px; height: 35px; border-radius: 50%; display: flex; 
        align-items: center; justify-content: center; color: #ccc; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s; text-decoration: none; z-index: 5;
    }
    .btn-fav:hover, .btn-fav.active { color: var(--promo-rouge); }

    .promo-flash { color: var(--promo-rouge); font-weight: 800; font-size: 0.9rem; text-transform: uppercase; margin-top: 10px; display: block; }
    .promo-badge { background: var(--promo-rouge); color: white; font-size: 0.75rem; padding: 2px 8px; border-radius: 5px; font-weight: bold; margin-left: 5px; }

    .price-old { text-decoration: line-through; color: #aaa; font-size: 0.85rem; }
    .price-new { color: var(--promo-rouge); font-weight: 800; font-size: 1.1rem; }
    .price-normal { color: var(--or-afro); font-weight: 700; font-size: 1.1rem; }

    .btn-buy { background: var(--or-afro); color: white; border: none; font-weight: 600; transition: 0.3s; }
    .btn-buy:hover { background: var(--or-fonce); color: white; }
</style>

<div class="page-header text-center">
    <div class="container">
        <h1 class="display-5 fw-bold text-uppercase">Collection AfroStyle</h1>
        <p class="lead">Le luxe et la tradition à portée de main</p>
        
        <div class="mx-auto mt-4" style="max-width: 500px;">
            <form action="" method="GET" class="input-group">
                <input type="text" name="q" class="form-control border-0 py-2 ps-4" style="border-radius: 30px 0 0 30px;" placeholder="Rechercher un modèle..." value="<?= htmlspecialchars($recherche) ?>">
                <button class="btn btn-warning px-4 text-white" style="border-radius: 0 30px 30px 0;" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="sidebar-filter shadow-sm p-3 sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-3 ps-2 border-start border-4 border-warning"> CATÉGORIES</h5>
                <a href="catalogue.php" class="cat-link <?= $cat_id == 0 ? 'active' : '' ?>">Toute la collection</a>
                <?php 
                $categories = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();
                foreach($categories as $c): ?>
                    <a href="?categorie=<?= $c['id_categorie'] ?>" class="cat-link <?= $cat_id == $c['id_categorie'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['nom_categorie']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm">
                <span class="fw-bold text-muted"><?= count($produits) ?> modèles trouvés</span>
                <div class="d-flex align-items-center gap-2">
                    <label class="small fw-bold text-nowrap">Trier par :</label>
                    <select class="form-select form-select-sm border-0 bg-light fw-bold" onchange="location.href='?categorie=<?= $cat_id ?>&q=<?= $recherche ?>&tri='+this.value">
                        <option value="new" <?= $tri == 'new' ? 'selected' : '' ?>>Nouveautés</option>
                        <option value="prix_asc" <?= $tri == 'prix_asc' ? 'selected' : '' ?>>Prix : Croissant</option>
                        <option value="prix_desc" <?= $tri == 'prix_desc' ? 'selected' : '' ?>>Prix : Décroissant</option>
                    </select>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($produits as $p): 
                    $reduc = $p['pourcentage_reduction'] ?? 0;
                    $p_base = $p['prix_unitaire'];
                    $p_final = $p_base * (1 - $reduc/100);
                    $is_fav = isset($_SESSION['favoris'][$p['id_produit']]);
                ?>
                <div class="col-6 col-md-4">
                    <div class="product-card shadow-sm p-3 text-center">
                        <div class="img-box rounded-3">
                            <a href="favoris_action.php?id=<?= $p['id_produit'] ?>" class="btn-fav <?= $is_fav ? 'active' : '' ?>">
                                <i class="bi <?= $is_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </a>
                            <img src="<?= htmlspecialchars($p['image_produit']) ?>" alt="<?= htmlspecialchars($p['nom_produit']) ?>">
                        </div>

                        <div class="promo-zone" style="min-height: 40px;">
                            <?php if ($reduc > 0): ?>
                                <span class="promo-flash">PROMO <span class="promo-badge">-<?= (int)$reduc ?>%</span></span>
                            <?php endif; ?>
                        </div>

                        <h6 class="fw-bold text-truncate mt-1"><?= htmlspecialchars($p['nom_produit']) ?></h6>

                        <div class="mb-3">
                            <?php if ($reduc > 0): ?>
                                <span class="price-old"><?= number_format($p_base, 0, '', ' ') ?> F</span><br>
                                <span class="price-new"><?= number_format($p_final, 0, '', ' ') ?> FCFA</span>
                            <?php else: ?>
                                <span class="price-normal"><?= number_format($p_base, 0, '', ' ') ?> FCFA</span>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2 mt-auto">
                            <a href="panier.php?action=add&id=<?= $p['id_produit'] ?>" class="btn btn-buy btn-sm py-2 rounded-3">
                                <i class="bi bi-cart-plus me-1"></i> PANIER
                            </a>
                            <a href="produit_detail.php?id=<?= $p['id_produit'] ?>" class="btn btn-outline-dark btn-sm py-2 rounded-3">DÉTAILS</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if(empty($produits)): ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Aucun modèle ne correspond à votre recherche.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once "commun/footer.php"; ?>