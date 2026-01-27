<?php
session_start();
require_once 'commun/connexiondb.php';

// --- CONFIGURATION PAGINATION ---
$parPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $parPage;

// --- FILTRES ET TRI ---
$cat_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$origine = $_GET['origine'] ?? ''; 
$recherche = $_GET['q'] ?? '';
$tri = $_GET['tri'] ?? 'new'; 
$today = date('Y-m-d');

// --- COMPTAGES DYNAMIQUES (POUR LA SIDEBAR) ---
$countCats = $pdo->query("SELECT id_categorie, COUNT(*) as nb FROM produit GROUP BY id_categorie")->fetchAll(PDO::FETCH_KEY_PAIR);
$countOrigins = $pdo->query("SELECT origine_modele, COUNT(*) as nb FROM produit WHERE origine_modele IS NOT NULL AND origine_modele != '' GROUP BY origine_modele")->fetchAll(PDO::FETCH_KEY_PAIR);

// --- PRÉPARATION DE LA RECHERCHE GLOBALE ---
$searchParam = "%$recherche%";

// 1. COMPTAGE TOTAL (Avec recherche étendue au nom, origine et catégorie)
$countSql = "SELECT COUNT(*) FROM produit p 
             LEFT JOIN categorie_produit c ON p.id_categorie = c.id_categorie 
             WHERE 1=1";

if ($cat_id > 0) $countSql .= " AND p.id_categorie = $cat_id";
if ($origine) $countSql .= " AND p.origine_modele = " . $pdo->quote($origine);
if ($recherche) {
    $countSql .= " AND (p.nom_produit LIKE :q OR p.origine_modele LIKE :q OR c.nom_categorie LIKE :q)";
}

$stmtCount = $pdo->prepare($countSql);
if ($recherche) $stmtCount->bindValue(':q', $searchParam);
$stmtCount->execute();
$totalProduits = $stmtCount->fetchColumn();
$totalPages = ceil($totalProduits / $parPage);

// 2. REQUÊTE PRINCIPALE
$sql = "SELECT p.*, c.nom_categorie, prom.pourcentage_reduction, prom.date_fin 
        FROM produit p 
        LEFT JOIN categorie_produit c ON p.id_categorie = c.id_categorie
        LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
        AND :today BETWEEN prom.date_debut AND prom.date_fin 
        WHERE 1=1";

if ($cat_id > 0) $sql .= " AND p.id_categorie = $cat_id";
if ($origine) $sql .= " AND p.origine_modele = " . $pdo->quote($origine);
if ($recherche) {
    $sql .= " AND (p.nom_produit LIKE :q OR p.origine_modele LIKE :q OR c.nom_categorie LIKE :q)";
}

switch ($tri) {
    case 'prix_asc': $sql .= " ORDER BY p.prix_unitaire ASC"; break;
    case 'prix_desc': $sql .= " ORDER BY p.prix_unitaire DESC"; break;
    default: $sql .= " ORDER BY p.id_produit DESC"; break;
}
$sql .= " LIMIT $parPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':today', $today);
if ($recherche) $stmt->bindValue(':q', $searchParam);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Catalogue - AfroStyle";
include_once "commun/header.php";
?>

<style>
    :root { 
        --afro-gold: #D4AF37; 
        --promo-rouge: #e63946;
        --fond-photo: #f5f2eb; 
    }
    body { background-color: #fdfdfd; font-family: 'Poppins', sans-serif; }
    
    .hero-header { 
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; 
        color: white; padding: 70px 0; margin-bottom: 40px;
    }

    .product-card {
        background: white; border: 1px solid #eee; border-radius: 20px;
        overflow: hidden; transition: 0.3s; height: 100%; 
        border-bottom: 6px solid var(--afro-gold); display: flex; flex-direction: column; position: relative;
    }
    .product-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0,0,0,0.08); }

    .img-wrapper { height: 270px; background-color: var(--fond-photo); display: flex; align-items: center; justify-content: center; position: relative; }
    .img-wrapper img { max-height: 85%; max-width: 85%; object-fit: contain; }
    
    .btn-fav {
        position: absolute; top: 15px; right: 15px; z-index: 10;
        background: rgba(255,255,255,0.9); border: none; border-radius: 50%; 
        width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1); color: #ccc; text-decoration: none;
    }
    .btn-fav.active { color: var(--promo-rouge); }

    .promo-tag { position: absolute; top: 15px; left: 15px; background: var(--promo-rouge); color: white; font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; font-weight: bold; z-index: 5; }
    
    .price-new { color: var(--afro-gold); font-weight: 800; font-size: 1.25rem; }
    .price-old { text-decoration: line-through; color: var(--promo-rouge); font-size: 0.85rem; margin-right: 8px; font-weight: 600; }

    .btn-decouvrir {
        background-color: var(--afro-gold);
        color: white;
        border: none;
        transition: 0.3s ease;
    }
    .btn-decouvrir:hover { background-color: #b8962d; color: white; transform: scale(1.02); }

    .sidebar-title { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: #444; margin-bottom: 12px; display: block; border-bottom: 2px solid var(--afro-gold); padding-bottom: 5px; margin-top: 20px; }
    .count-badge { font-size: 0.7rem; background: #f0f0f0; color: #888; padding: 2px 8px; border-radius: 10px; float: right; margin-top: 3px; }
    .text-warning .count-badge { background: var(--afro-gold); color: white; }
</style>

<div class="hero-header text-center">
    <div class="container">
        <h1 class="display-4 fw-bold text-uppercase">Notre Catalogue</h1>
        <p class="lead">Un coup d'œil sur les produits qui vous feront craquer !</p>
        <div class="mx-auto mt-4" style="max-width: 550px;">
            <form action="" method="GET" class="d-flex shadow-lg rounded-pill bg-white overflow-hidden border">
                <input type="text" name="q" class="form-control border-0 py-2 ps-4" placeholder="Rechercher produit, pays, style..." value="<?= htmlspecialchars($recherche) ?>">
                <button class="btn btn-dark px-4" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold text-muted m-0"><i class="bi bi-grid-3x3-gap me-2"></i><?= $totalProduits ?> Articles trouvés</h6>
                <select class="form-select border-0 shadow-sm bg-white rounded-pill w-auto" onchange="location.href='?categorie=<?= $cat_id ?>&origine=<?= urlencode($origine) ?>&q=<?= urlencode($recherche) ?>&tri='+this.value">
                    <option value="new" <?= $tri == 'new' ? 'selected' : '' ?>>Nouveautés</option>
                    <option value="prix_asc" <?= $tri == 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="prix_desc" <?= $tri == 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                </select>
            </div>

            <div class="row g-4">
                <?php foreach ($produits as $p): 
                    $reduc = $p['pourcentage_reduction'] ?? 0;
                    $p_final = $p['prix_unitaire'] * (1 - $reduc/100);
                    $is_fav = (isset($_SESSION['favoris'][$p['id_produit']]));
                ?>
                <div class="col-6 col-md-4">
                    <div class="product-card">
                        <div class="img-wrapper">
                            <a href="favoris.php?add=<?= $p['id_produit'] ?>" class="btn-fav <?= $is_fav ? 'active' : '' ?>">
                                <i class="bi <?= $is_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </a>
                            <?php if ($reduc > 0): ?>
                                <div class="promo-tag">PROMO -<?= (int)$reduc ?>%</div>
                            <?php endif; ?>
                            <img src="<?= str_replace(' ', '%20', trim($p['image_produit'])) ?>" onerror="this.src='public/Images/placeholder.png'">
                        </div>

                        <div class="p-3 text-center">
                            <div class="text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #999; font-weight: 500;">
                                <i class="bi bi-geo-alt-fill" style="color: var(--afro-gold);"></i> <?= htmlspecialchars($p['origine_modele'] ?: 'AfroStyle') ?>
                            </div>

                            <h5 class="fw-bold mb-2 text-truncate text-dark" style="font-size: 1.1rem;">
                                <?= htmlspecialchars($p['nom_produit']) ?>
                            </h5>

                            <div class="mb-3">
                                <?php if ($reduc > 0): ?>
                                    <span class="price-old"><?= number_format($p['prix_unitaire'], 0, '', ' ') ?></span>
                                <?php endif; ?>
                                <span class="price-new"><?= number_format($p_final, 0, '', ' ') ?> <small style="font-size: 0.7rem;">FCFA</small></span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="panier.php?action=add&id=<?= $p['id_produit'] ?>" class="btn btn-dark rounded-pill fw-bold btn-sm py-2">
                                    <i class="bi bi-cart-plus me-2"></i>AU PANIER
                                </a>
                                <a href="produit_detail.php?id=<?= $p['id_produit'] ?>" class="btn btn-decouvrir btn-sm rounded-pill fw-bold py-2">
                                    DÉCOUVRIR
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if($totalPages > 1): ?>
            <nav class="mt-5"><ul class="pagination justify-content-center">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?page=<?= $i ?>&categorie=<?= $cat_id ?>&origine=<?= urlencode($origine) ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul></nav>
            <?php endif; ?>
        </div>

        <div class="col-lg-3">
            <div class="bg-white p-4 rounded-4 shadow-sm border sticky-top" style="top: 20px;">
                <span class="sidebar-title mt-0">Styles</span>
                <div class="list-group list-group-flush mb-4">
                    <a href="?origine=<?= urlencode($origine) ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>" class="list-group-item list-group-item-action border-0 px-0 small <?= $cat_id == 0 ? 'fw-bold text-warning' : '' ?>">
                        Tout voir <span class="count-badge"><?= array_sum($countCats) ?></span>
                    </a>
                    <?php 
                    $categories_list = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();
                    foreach($categories_list as $c): 
                        $nb = $countCats[$c['id_categorie']] ?? 0;
                    ?>
                        <a href="?categorie=<?= $c['id_categorie'] ?>&origine=<?= urlencode($origine) ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>" class="list-group-item list-group-item-action border-0 px-0 small <?= $cat_id == $c['id_categorie'] ? 'fw-bold text-warning' : '' ?>">
                            <?= htmlspecialchars($c['nom_categorie']) ?> <span class="count-badge"><?= $nb ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <span class="sidebar-title">Origines</span>
                <div class="list-group list-group-flush">
                    <a href="?categorie=<?= $cat_id ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>" class="list-group-item list-group-item-action border-0 px-0 small <?= $origine == '' ? 'fw-bold text-warning' : '' ?>">
                        Toutes les origines <span class="count-badge"><?= array_sum($countOrigins) ?></span>
                    </a>
                    <?php foreach($countOrigins as $nom_o => $nb_o): ?>
                        <a href="?origine=<?= urlencode($nom_o) ?>&categorie=<?= $cat_id ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>" class="list-group-item list-group-item-action border-0 px-0 small <?= $origine == $nom_o ? 'fw-bold text-warning' : '' ?>">
                            <?= htmlspecialchars($nom_o) ?> <span class="count-badge"><?= $nb_o ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "commun/footer.php"; ?>