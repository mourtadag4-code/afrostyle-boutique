<?php
session_start();
require_once "commun/connexiondb.php";

$id_user = $_SESSION['utilisateur_id'] ?? null;

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['remove'])) {
    $id_rem = (int)$_GET['remove'];
    if (isset($_SESSION['favoris'][$id_rem])) {
        unset($_SESSION['favoris'][$id_rem]);
    }
    if ($id_user) {
        $stmt = $pdo->prepare("DELETE FROM favoris WHERE id_utilisateur = ? AND id_produit = ?");
        $stmt->execute([$id_user, $id_rem]);
    }
    header("Location: favoris.php");
    exit;
}

$page_title = "Mes Favoris - AFROSTYLE";
include_once "commun/header.php";
?>

<style>
    :root { --afro-gold: #D4AF37; --afro-orange: #E97451; }
    body { background-color: #fdfdfd; font-family: 'Poppins', sans-serif; }
    .hero-header { 
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; 
        color: white; padding: 60px 0; 
    }
    .fav-card {
        background: white; border: 1px solid #eee; border-radius: 20px;
        overflow: hidden; transition: 0.3s; height: 100%; border-bottom: 5px solid var(--afro-gold);
    }
    .fav-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .img-wrapper { height: 280px; position: relative; overflow: hidden; background: #f9f9f9; }
    .img-fav { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    .btn-delete {
        position: absolute; top: 10px; right: 10px; z-index: 10;
        background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 35px; height: 35px;
        color: #e74c3c; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>

<div class="hero-header text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold text-uppercase">Mes Coups de Cœur</h1>
        <p class="lead">Retrouvez ici tous les modèles qui vous ont fait craquer</p>
    </div>
</div>

<div class="container mb-5">
    <?php 
    $favoris_session = $_SESSION['favoris'] ?? [];

    if (empty($favoris_session)): 
    ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <i class="bi bi-heart text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            <h4 class="mt-3">Votre liste est vide</h4>
            <a href="catalogue.php" class="btn btn-warning mt-3 px-5 rounded-pill fw-bold">Retour au catalogue</a>
        </div>
    <?php 
    else: 
        $ids_favoris = array_keys($favoris_session);
        $placeholders = implode(',', array_fill(0, count($ids_favoris), '?'));
        
        // On récupère les produits ET on vérifie s'il y a une promo aujourd'hui
        $today = date('Y-m-d');
        $sql = "SELECT p.*, prom.pourcentage_reduction 
                FROM produit p 
                LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 
                AND ? BETWEEN prom.date_debut AND prom.date_fin
                WHERE p.id_produit IN ($placeholders)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$today], $ids_favoris));
        $produitsFavoris = $stmt->fetchAll();
    ?>
        <div class="row g-4">
            <?php foreach ($produitsFavoris as $row): 
                // Calcul du prix affiché (avec promo si elle existe)
                $prix_base = $row['prix_unitaire'];
                $remise = $row['pourcentage_reduction'] ?? 0;
                $prix_affiche = ($remise > 0) ? round($prix_base * (1 - ($remise / 100))) : $prix_base;
            ?>
                <div class="col-md-4 col-lg-3">
                    <div class="fav-card shadow-sm">
                        <div class="img-wrapper">
                            <a href="favoris.php?remove=<?= $row['id_produit'] ?>" class="btn-delete">
                                <i class="bi bi-heart-fill"></i>
                            </a>
                            <img src="<?= $row['image_produit'] ?>" class="img-fav">
                        </div>
                        
                        <div class="p-3 text-center">
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($row['nom_produit']) ?></h6>
                            <p class="fw-bold mb-3" style="color: var(--afro-gold);">
                                <?php if($remise > 0): ?>
                                    <span class="text-decoration-line-through text-muted small me-2"><?= number_format($prix_base, 0, '', ' ') ?></span>
                                <?php endif; ?>
                                <?= number_format($prix_affiche, 0, '', ' ') ?> FCFA
                            </p>
                            
                            <a href="panier.php?action=add&id=<?= $row['id_produit'] ?>" class="btn btn-dark w-100 rounded-pill btn-sm py-2">
                                <i class="bi bi-cart-plus me-1"></i> Ajouter au panier
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once "commun/footer.php"; ?>