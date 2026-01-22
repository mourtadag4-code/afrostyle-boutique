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

// --- CONFIG PAGINATION FAVORIS ---
$fav_ids = isset($_SESSION['favoris']) ? array_keys($_SESSION['favoris']) : [];
$parPageFav = 12; // Augmenté à 12 pour remplir les lignes de 4 produits
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalFavs = count($fav_ids);
$totalPages = ceil($totalFavs / $parPageFav);
$offset = ($page - 1) * $parPageFav;

$page_title = "Mes Favoris - AfroStyle";
include_once "commun/header.php";
?>

<style>
    :root { --afro-gold: #D4AF37; }
    .hero-header { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; color: white; padding: 70px 0; }
    .fav-card { background: white; border-radius: 20px; border-bottom: 5px solid var(--afro-gold); overflow: hidden; height: 100%; transition: 0.3s; display: flex; flex-direction: column; }
    .fav-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .img-wrapper { height: 250px; background: #f5f2eb; display: flex; align-items: center; justify-content: center; position: relative; }
    .img-wrapper img { max-width: 80%; max-height: 80%; object-fit: contain; }
    .btn-delete { position: absolute; top: 10px; right: 10px; color: #e74c3c; background: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .pagination .page-item.active .page-link { background: var(--afro-gold); border-color: var(--afro-gold); color: white; border-radius: 50%; }
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
            <a href="catalogue.php" class="btn btn-warning mt-3 rounded-pill text-white fw-bold px-4 shadow-sm">Découvrir nos créations</a>
        </div>
    <?php else: 
        // On découpe le tableau pour la page actuelle
        $ids_page = array_slice($fav_ids, $offset, $parPageFav);
        
        if(!empty($ids_page)) {
            $placeholders = implode(',', array_fill(0, count($ids_page), '?'));
            $today = date('Y-m-d');
            $stmt = $pdo->prepare("SELECT p.*, prom.pourcentage_reduction 
                                   FROM produit p 
                                   LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
                                   AND ? BETWEEN prom.date_debut AND prom.date_fin
                                   WHERE p.id_produit IN ($placeholders)");
            $stmt->execute(array_merge([$today], $ids_page));
            $produits = $stmt->fetchAll();
        } else {
            $produits = [];
        }
    ?>
        <div class="row g-4">
            <?php foreach ($produits as $row): 
                $reduc = $row['pourcentage_reduction'] ?? 0;
                $prix_final = $row['prix_unitaire'] * (1 - $reduc/100);
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="fav-card shadow-sm">
                        <div class="img-wrapper">
                            <a href="favoris.php?remove=<?= $row['id_produit'] ?>" class="btn-delete" title="Supprimer">
                                <i class="bi bi-heart-fill"></i>
                            </a>
                            <img src="<?= htmlspecialchars($row['image_produit']) ?>" onerror="this.src='public/Images/placeholder.png'">
                        </div>
                        <div class="p-3 text-center mt-auto">
                            <h6 class="fw-bold text-truncate"><?= htmlspecialchars($row['nom_produit']) ?></h6>
                            <p class="fw-bold mb-3" style="color:var(--afro-gold);">
                                <?= number_format($prix_final, 0, '', ' ') ?> FCFA
                            </p>
                            <a href="panier.php?action=add&id=<?= $row['id_produit'] ?>" class="btn btn-dark btn-sm w-100 rounded-pill fw-bold">
                                <i class="bi bi-cart-plus"></i> PANIER
                            </a>
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
                        <a class="page-link shadow-sm border-0 mx-1 rounded-pill" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include_once "commun/footer.php"; ?>