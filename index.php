<?php
// 1. DÉMARRAGE DE LA SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. INCLUSION DU HEADER
require_once 'commun/header.php';

// 3. VARIABLES DE PERSONNALISATION
$message_principal = '';
$sous_message = '';

// On vérifie si l'utilisateur est connecté via l'ID
if(isset($_SESSION['utilisateur_id'])) {
    // Sécurité : si utilisateur_nom est vide, on affiche "Cher Client" pour éviter le Warning
    $nom_complet = htmlspecialchars($_SESSION['utilisateur_nom'] ?? 'Cher Client'); 
    
    $message_principal = "Heureux de vous revoir, <span class='text-warning'>$nom_complet</span> !";
    $sous_message = "Découvrez les nouveautés sélectionnées pour vous";
} else {
    $message_principal = "Bienvenue chez <span class='text-warning'>AFROSTYLE SHOP</span>";
    $sous_message = "Découvrez notre collection exclusive de tenues africaines authentiques et modernes. L'élégance africaine réinventée pour votre quotidien et vos cérémonies.";
}
?>

<header class="bg-dark text-white py-5" style="
    background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)),
                url('public/Images/banniere.png');
    background-size: cover;
    background-position: center;
    min-height: 90vh;
    display: flex;
    align-items: center;
    margin-top: -1px;
">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start">
                <h1 class="display-4 fw-bold mb-3">
                    <?php echo $message_principal; ?>
                </h1>
                <p class="lead mb-4">
                    <?php echo $sous_message; ?>
                </p>

                <form action="catalogue.php" method="GET" class="input-group input-group-lg mb-4 justify-content-center justify-content-lg-start">
                    <input type="text" name="q" class="form-control" placeholder="Rechercher une tenue, un style, un accessoire...">
                    <button class="btn btn-warning" type="submit">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </form>
                
                <?php if(isset($_SESSION['utilisateur_id'])): ?>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start mt-3">
                    <a href="catalogue.php" class="btn btn-outline-light">
                        <i class="bi bi-star-fill"></i> Voir les nouveautés
                    </a>
                    <a href="favoris.php" class="btn btn-outline-light">
                        <i class="bi bi-heart"></i> Mes favoris
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<section class="section-afro py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">
                <?php echo isset($_SESSION['utilisateur_id']) ? "Vos collections préférées" : "Nos Collections Populaires"; ?>
            </h2>
            <p class="text-muted">
                <?php echo isset($_SESSION['utilisateur_id']) ? 
                    "Découvrez les collections que vous appréciez le plus" : 
                    "Découvrez l'artisanat africain à travers nos différentes collections"; ?>
            </p>
        </div>

        <div class="d-flex flex-wrap justify-content-between gap-3 text-center">
            <div class="category-item-afro flex-fill">
                <div class="product-card-afro">
                    <img src="public/Images/gaouni.png" alt="Gaouni" style="height: 150px; object-fit: contain;">
                    <h3 class="product-name-afro mt-2">Gaouni</h3>
                </div>
            </div>

            <div class="category-item-afro flex-fill">
                <div class="product-card-afro">
                    <img src="public/Images/salouva.png" alt="Salouva" style="height: 150px; object-fit: contain;">
                    <h3 class="product-name-afro mt-2">Salouva</h3>
                </div>
            </div>

            <div class="category-item-afro flex-fill">
                <div class="product-card-afro">
                    <img src="public/Images/kandzou.PNG" alt="Kandzou" style="height: 150px; object-fit: contain;">
                    <h3 class="product-name-afro mt-2">Kandzou</h3>
                </div>
            </div>

            <div class="category-item-afro flex-fill">
                <div class="product-card-afro">
                    <img src="public/Images/kofia.png" alt="Kofia" style="height: 150px; object-fit: contain;">
                    <h3 class="product-name-afro mt-2">Kofia</h3>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="catalogue.php" class="btn btn-outline-warning">
                <i class="bi bi-grid me-2"></i> 
                <?php echo isset($_SESSION['utilisateur_id']) ? "Explorer le catalogue" : "Voir toutes les catégories"; ?>
            </a>
        </div>
    </div>
</section>

<section class="benefits-section-afro py-5 bg-light">
    <div class="container text-center">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                <h5 class="mt-2">Produits Authentiques</h5>
            </div>
            <div class="col-lg-3 col-md-6">
                <i class="bi bi-truck text-primary fs-1"></i>
                <h5 class="mt-2">Livraison Rapide</h5>
            </div>
            <div class="col-lg-3 col-md-6">
                <i class="bi bi-shield-check text-warning fs-1"></i>
                <h5 class="mt-2">Paiement Sécurisé</h5>
            </div>
            <div class="col-lg-3 col-md-6">
                <i class="bi bi-chat-heart text-danger fs-1"></i>
                <h5 class="mt-2">Avis Vérifiés</h5>
            </div>
        </div>
    </div>
</section>

<?php require_once 'commun/footer.php'; ?>