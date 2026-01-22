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
$recherche = $_GET['q'] ?? '';
$tri = $_GET['tri'] ?? 'new'; 
$today = date('Y-m-d');

// 1. COMPTAGE TOTAL
$countSql = "SELECT COUNT(*) FROM produit WHERE 1=1";
if ($cat_id > 0) $countSql .= " AND id_categorie = $cat_id";
if ($recherche) $countSql .= " AND nom_produit LIKE " . $pdo->quote('%'.$recherche.'%');
$totalProduits = $pdo->query($countSql)->fetchColumn();
$totalPages = ceil($totalProduits / $parPage);

// 2. REQUÊTE PRINCIPALE (Avec Promotions)
$sql = "SELECT p.*, prom.pourcentage_reduction, prom.date_fin 
        FROM produit p 
        LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
        AND ? BETWEEN prom.date_debut AND prom.date_fin 
        WHERE 1=1";

if ($cat_id > 0) $sql .= " AND p.id_categorie = $cat_id";
if ($recherche) $sql .= " AND p.nom_produit LIKE " . $pdo->quote('%'.$recherche.'%');

switch ($tri) {
    case 'prix_asc': $sql .= " ORDER BY p.prix_unitaire ASC"; break;
    case 'prix_desc': $sql .= " ORDER BY p.prix_unitaire DESC"; break;
    default: $sql .= " ORDER BY p.id_produit DESC"; break;
}
$sql .= " LIMIT $parPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute([$today]);
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
        background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('public/Images/banner.jpg') center/cover; 
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
        box-shadow: 0 3px 8px rgba(0,0,0,0.1); transition: 0.3s; color: #ccc; text-decoration: none;
    }
    .btn-fav.active { color: var(--promo-rouge); }

    /* Tags et Prix Promo */
    .promo-tag { position: absolute; top: 15px; left: 15px; background: var(--promo-rouge); color: white; font-size: 0.75rem; padding: 5px 12px; border-radius: 50px; font-weight: bold; }
    .price-new { color: var(--afro-gold); font-weight: 800; font-size: 1.2rem; }
    .price-old { text-decoration: line-through; color: #bbb; font-size: 0.9rem; margin-right: 6px; }
    .promo-expire { font-size: 0.65rem; color: var(--promo-rouge); font-weight: 700; text-transform: uppercase; margin-bottom: 5px; display: block;}

    .pagination .page-item.active .page-link { background-color: var(--afro-gold); border-color: var(--afro-gold); color: white; border-radius: 50%; }
</style>

<div class="hero-header text-center">
    <div class="container">
        <h1 class="display-5 fw-bold text-uppercase mb-2">Notre Collection</h1>
        <p class="lead mb-4">Prêt à porter vos plus belles tenues AfroStyle ?</p>
        
        <div class="mx-auto" style="max-width: 550px;">
            <form action="" method="GET" class="d-flex shadow-lg" style="border-radius: 50px; overflow: hidden; background: white;">
                <input type="text" name="q" class="form-control border-0 py-3 ps-4" placeholder="Rechercher..." value="<?= htmlspecialchars($recherche) ?>">
                <button class="btn btn-dark px-4" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="bg-white p-4 rounded-4 shadow-sm border mb-4">
                <h6 class="fw-bold mb-3 border-start border-4 border-warning ps-2 text-uppercase small">Catégories</h6>
                <div class="list-group list-group-flush mb-4">
                    <a href="catalogue.php" class="list-group-item list-group-item-action border-0 px-0 <?= $cat_id == 0 ? 'fw-bold text-warning' : '' ?>">Toutes les créations</a>
                    <?php 
                    $categories = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();
                    foreach($categories as $c): ?>
                        <a href="?categorie=<?= $c['id_categorie'] ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>" class="list-group-item list-group-item-action border-0 px-0 <?= $cat_id == $c['id_categorie'] ? 'fw-bold text-warning' : '' ?>">
                            <?= htmlspecialchars($c['nom_categorie']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <h6 class="fw-bold mb-3 border-start border-4 border-warning ps-2 text-uppercase small">Trier par</h6>
                <select class="form-select border-0 bg-light rounded-pill" onchange="location.href='?categorie=<?= $cat_id ?>&q=<?= urlencode($recherche) ?>&tri='+this.value">
                    <option value="new" <?= $tri == 'new' ? 'selected' : '' ?>>Nouveautés</option>
                    <option value="prix_asc" <?= $tri == 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="prix_desc" <?= $tri == 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                </select>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row g-4">
                <?php foreach ($produits as $p): 
                    $reduc = $p['pourcentage_reduction'] ?? 0;
                    $p_final = $p['prix_unitaire'] * (1 - $reduc/100);
                    $is_fav = (isset($_SESSION['favoris'][$p['id_produit']]));
                    $lien_action = $is_fav ? "favoris.php?remove=".$p['id_produit']."&from=cat" : "favoris.php?add=".$p['id_produit'];
                ?>
                <div class="col-6 col-md-4">
                    <div class="product-card">
                        <div class="img-wrapper">
                            <a href="<?= $lien_action ?>" class="btn-fav <?= $is_fav ? 'active' : '' ?>">
                                <i class="bi <?= $is_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </a>

                            <?php if ($reduc > 0): ?>
                                <div class="promo-tag">PROMO -<?= (int)$reduc ?>%</div>
                            <?php endif; ?>
                            
                            <img src="<?= str_replace(' ', '%20', trim($p['image_produit'])) ?>" onerror="this.src='public/Images/placeholder.png'">
                        </div>

                        <div class="p-3 text-center">
                            <h6 class="fw-bold mb-2 text-truncate"><?= htmlspecialchars($p['nom_produit']) ?></h6>
                            
                            <div class="mb-3">
                                <?php if ($reduc > 0): ?>
                                    <span class="promo-expire"><i class="bi bi-clock"></i> Fin : <?= date('d/m', strtotime($p['date_fin'])) ?></span>
                                    <span class="price-old"><?= number_format($p['prix_unitaire'], 0, '', ' ') ?></span>
                                <?php endif; ?>
                                <span class="price-new"><?= number_format($p_final, 0, '', ' ') ?> FCFA</span>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="panier.php?action=add&id=<?= $p['id_produit'] ?>" class="btn btn-dark rounded-pill btn-sm py-2 fw-bold">PANIER</a>
                                <a href="produit_detail.php?id=<?= $p['id_produit'] ?>" class="text-muted small text-decoration-none mt-1">VOIR DÉTAILS</a>
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
                            <a class="page-link shadow-sm border-0 mx-1" href="?page=<?= $i ?>&categorie=<?= $cat_id ?>&q=<?= urlencode($recherche) ?>&tri=<?= $tri ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once "commun/footer.php"; ?>