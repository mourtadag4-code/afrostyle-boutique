<?php
session_start();
require_once 'commun/connexiondb.php';

// --- CONFIGURATION PAGINATION ---
$parPage = 6; 
$pageCourante = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pageCourante < 1) $pageCourante = 1;
$offset = ($pageCourante - 1) * $parPage;

// --- FILTRES ET TRI ---
$cat_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$recherche = $_GET['q'] ?? '';
$tri = $_GET['tri'] ?? 'new'; 
$today = date('Y-m-d');

// 1. COMPTAGE TOTAL (Pagination)
$countSql = "SELECT COUNT(*) FROM produit WHERE 1=1";
if ($cat_id > 0) $countSql .= " AND id_categorie = $cat_id";
if ($recherche) $countSql .= " AND nom_produit LIKE " . $pdo->quote('%'.$recherche.'%');
$totalProduits = $pdo->query($countSql)->fetchColumn();
$totalPages = ceil($totalProduits / $parPage);

// 2. REQUÊTE PRINCIPALE (Jointure Promotion, Favoris, etc.)
$sql = "SELECT p.*, prom.pourcentage_reduction, prom.date_fin 
        FROM produit p 
        LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
        AND ? BETWEEN prom.date_debut AND prom.date_fin 
        WHERE 1=1";

if ($cat_id > 0) $sql .= " AND p.id_categorie = $cat_id";
if ($recherche) $sql .= " AND p.nom_produit LIKE " . $pdo->quote('%'.$recherche.'%');

// Tri
switch ($tri) {
    case 'prix_asc': $sql .= " ORDER BY p.prix_unitaire ASC"; break;
    case 'prix_desc': $sql .= " ORDER BY p.prix_unitaire DESC"; break;
    default: $sql .= " ORDER BY p.id_produit DESC"; break;
}

$sql .= " LIMIT $parPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute([$today]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Collection AfroStyle";
include_once "commun/header.php";
?>

<style>
    :root { --or-afro: #D4AF37; --or-fonce: #B8860B; --promo-rouge: #e63946; }
    .product-card { border: none; border-radius: 15px; background: #fff; transition: 0.3s; height: 100%; display: flex; flex-direction: column; overflow: hidden; position: relative; }
    .product-card:hover { transform: translateY(-10px); box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important; }
    .img-box { height: 250px; background: #fdfaf3; display: flex; align-items: center; justify-content: center; position: relative; padding: 10px; }
    .img-box img { max-height: 100%; max-width: 100%; object-fit: contain; }
    
    /* Coeur Favoris */
    .btn-fav { position: absolute; top: 15px; right: 15px; background: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ddd; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: 0.3s; text-decoration: none; z-index: 10; }
    .btn-fav:hover, .btn-fav.active { color: var(--promo-rouge); background: #fff1f2; }

    /* Badge Promo */
    .promo-badge { position: absolute; top: 15px; left: 15px; background: var(--promo-rouge); color: white; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.8rem; z-index: 10; }

    .price-new { color: var(--promo-rouge); font-weight: 800; font-size: 1.2rem; }
    .price-old { text-decoration: line-through; color: #aaa; font-size: 0.9rem; margin-right: 8px; }
    .price-normal { color: var(--or-afro); font-weight: 800; font-size: 1.2rem; }

    .btn-buy { background: var(--or-afro); color: white; border: none; font-weight: bold; }
    .btn-buy:hover { background: var(--or-fonce); color: white; }
    
    /* Pagination */
    .page-link { border: none; color: #333; margin: 0 4px; border-radius: 8px !important; font-weight: bold; }
    .page-item.active .page-link { background: var(--or-afro) !important; color: white !important; }
</style>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="bg-white p-4 rounded-4 shadow-sm">
                <h5 class="fw-bold mb-4 border-start border-4 border-warning ps-3">NOS CATÉGORIES</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="catalogue.php" class="text-decoration-none <?= $cat_id == 0 ? 'text-warning fw-bold' : 'text-dark' ?>">Tout voir</a></li>
                    <?php 
                    $cats = $pdo->query("SELECT * FROM categorie_produit")->fetchAll();
                    foreach($cats as $c): ?>
                        <li class="mb-2">
                            <a href="?categorie=<?= $c['id_categorie'] ?>" class="text-decoration-none <?= $cat_id == $c['id_categorie'] ? 'text-warning fw-bold' : 'text-dark' ?>">
                                <?= htmlspecialchars($c['nom_categorie']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                <h6 class="mb-0 fw-bold"><?= $totalProduits ?> Articles trouvés</h6>
                <select class="form-select w-auto border-0 bg-light fw-bold" onchange="location.href='?categorie=<?= $cat_id ?>&q=<?= $recherche ?>&tri='+this.value">
                    <option value="new" <?= $tri == 'new' ? 'selected' : '' ?>>Nouveautés</option>
                    <option value="prix_asc" <?= $tri == 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="prix_desc" <?= $tri == 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                </select>
            </div>

            <div class="row g-4">
                <?php foreach ($produits as $p): 
                    $reduc = $p['pourcentage_reduction'] ?? 0;
                    $prix_final = $p['prix_unitaire'] * (1 - $reduc/100);
                    $is_fav = (isset($_SESSION['favoris']) && in_array($p['id_produit'], $_SESSION['favoris']));

                    // NETTOYAGE URL IMAGE (Git & Espaces)
                    $img_path = trim($p['image_produit']);
                    $img_url = str_replace(' ', '%20', $img_path);
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="product-card shadow-sm p-3">
                        <?php if($reduc > 0): ?>
                            <div class="promo-badge">-<?= (int)$reduc ?>%</div>
                        <?php endif; ?>

                        <a href="favoris_action.php?id=<?= $p['id_produit'] ?>" class="btn-fav <?= $is_fav ? 'active' : '' ?>">
                            <i class="bi <?= $is_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        </a>

                        <div class="img-box rounded-4">
                            <img src="<?= $img_url ?>" alt="<?= htmlspecialchars($p['nom_produit']) ?>" onerror="this.src='public/Images/placeholder.png'">
                        </div>

                        <div class="card-body text-center mt-3 px-0">
                            <h6 class="fw-bold text-truncate"><?= htmlspecialchars($p['nom_produit']) ?></h6>
                            
                            <div class="mb-3">
                                <?php if($reduc > 0): ?>
                                    <span class="price-old"><?= number_format($p['prix_unitaire'], 0, '', ' ') ?> F</span>
                                    <span class="price-new"><?= number_format($prix_final, 0, '', ' ') ?> FCFA</span>
                                <?php else: ?>
                                    <span class="price-normal"><?= number_format($p['prix_unitaire'], 0, '', ' ') ?> FCFA</span>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="panier.php?action=add&id=<?= $p['id_produit'] ?>" class="btn btn-buy rounded-pill">
                                    <i class="bi bi-cart-plus me-1"></i> PANIER
                                </a>
                                <a href="produit_detail.php?id=<?= $p['id_produit'] ?>" class="btn btn-outline-dark btn-sm border-0 fw-bold">VOIR DÉTAILS</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $pageCourante) ? 'active' : '' ?>">
                            <a class="page-link shadow-sm" href="?page=<?= $i ?>&categorie=<?= $cat_id ?>&tri=<?= $tri ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once "commun/footer.php"; ?>