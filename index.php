<?php
// 1. DÉMARRAGE DE LA SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. INCLUSION DU HEADER
include_once 'commun/header.php';

// 3. LOGIQUE DE PERSONNALISATION
$message_principal = '';
$sous_message = '';

if(isset($_SESSION['utilisateur_id'])) {
    $nom_complet = htmlspecialchars($_SESSION['utilisateur_nom'] ?? 'Cher Client'); 
    $message_principal = "Heureux de vous revoir, <span class='text-warning'>$nom_complet</span> !";
    $sous_message = "Découvrez les nouveautés sélectionnées pour vous.";
} else {
    $message_principal = "Bienvenue chez <span class='text-warning'>AFROSTYLE SHOP</span>";
    $sous_message = "L'élégance africaine réinventée pour votre quotidien et vos cérémonies.";
}
?>

<style>
    /* Design des cartes */
    .img-container-afro {
        position: relative;
        height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f3e9dc; /* Fond terre d'Afrique */
        border-radius: 15px 15px 0 0;
        overflow: hidden;
    }

    .img-container-afro img {
        max-height: 85%;
        max-width: 85%;
        object-fit: contain;
        transition: all 0.4s ease;
        filter: brightness(0.96) contrast(1.05);
    }

    .product-card-afro {
        background: white;
        border-radius: 15px;
        border: 1px solid #e2d1c3;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .product-card-afro:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(93, 64, 55, 0.15);
    }

    /* Badge Pays - Épuré sans drapeau */
    .country-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.95);
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #5d4037;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        z-index: 2;
    }

    /* Zone de texte */
    .desc-box-afro {
        padding: 25px 15px;
        text-align: center;
    }

    .product-name-afro {
        font-size: 1.35rem;
        font-weight: 800;
        color: #b8860b; /* Doré Royal */
        margin-bottom: 12px;
        min-height: 2.2em;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-desc-text {
        font-size: 1.1rem;
        color: #4e342e;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .banner-afro {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banniere.png');
        background-size: cover;
        background-position: center;
        min-height: 75vh;
        display: flex;
        align-items: center;
        color: white;
    }

    .hero-catchphrase {
        font-size: 1.75rem !important;
        line-height: 1.6;
        color: #5d4037;
        font-style: italic;
        letter-spacing: 0.3px;
    }
</style>

<header class="banner-afro">
    <div class="container text-center text-lg-start">
        <h1 class="display-3 fw-bold mb-3"><?php echo $message_principal; ?></h1>
        <p class="lead mb-4 fs-4"><?php echo $sous_message; ?></p>
        <form action="catalogue.php" method="GET" class="input-group input-group-lg mb-4 shadow-sm" style="max-width: 600px;">
            <input type="text" name="q" class="form-control border-0" placeholder="Rechercher un style, un pays...">
            <button class="btn btn-warning px-4" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>
</header>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-5" style="color: #3e2723;">Nos Collections Populaires</h2>
            <div class="mx-auto bg-warning" style="height: 4px; width: 80px; margin-bottom: 25px;"></div>
            <p class="hero-catchphrase mx-auto" style="max-width: 950px;">
                "L'excellence du savoir-faire africain à travers une sélection de pièces uniques, alliant tradition ancestrale et élégance contemporaine."
            </p>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <?php
            $items = [
                ['img' => 'gaouni.png', 'name' => 'Gaouni', 'pays' => 'Comores', 'desc' => 'Tenue noble en soie fine, réservée aux cérémonies du Grand Mariage.'],
                ['img' => 'Shiromani.png', 'name' => 'Shiromani', 'pays' => 'Comores', 'desc' => 'Le voile traditionnel aux six motifs, symbole de pudeur et d\'élégance.'],
                ['img' => 'nigerienne1.png', 'name' => 'Agbada', 'pays' => 'Nigéria', 'desc' => 'Le majestueux boubou trois pièces en coton damassé de haute qualité.'],
                ['img' => 'nigerienne2.png', 'name' => 'Habit Traditionnel', 'pays' => 'Nigéria', 'desc' => 'Ensemble d\'apparat richement brodé pour une célébration inoubliable.'],
                ['img' => 'grand-boubou.png', 'name' => 'Boubou Sénégal', 'pays' => 'Sénégal', 'desc' => 'Bazin riche et brillant, coupé selon la tradition des grands maîtres.'],
                ['img' => 'Grand-boubou-africain.png', 'name' => 'Boubou Bazin', 'pays' => 'Sénégal', 'desc' => 'Le célèbre Bazin teinté à la main, réputé pour son éclat exceptionnel.'],
                ['img' => 'guinee1.PNG', 'name' => 'Lepi', 'pays' => 'Guinée', 'desc' => 'Le précieux tissu indigo tissé par les artisans des hauts plateaux.'],
                ['img' => 'guinee2.png', 'name' => 'Leppi Men', 'pays' => 'Guinée', 'desc' => 'L\'élégance du tissage guinéen déclinée pour l\'homme moderne.']
            ];

            foreach ($items as $item) : ?>
            <div class="col">
                <div class="product-card-afro h-100 shadow-sm">
                    <div class="img-container-afro">
                        <span class="country-badge"><?php echo $item['pays']; ?></span>
                        <img src="public/Images/<?php echo $item['img']; ?>" alt="<?php echo $item['name']; ?>" onerror="this.src='public/Images/placeholder.png'">
                    </div>
                    <div class="desc-box-afro">
                        <h3 class="product-name-afro"><?php echo $item['name']; ?></h3>
                        <p class="product-desc-text"><?php echo $item['desc']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="catalogue.php" class="btn btn-lg btn-warning px-5 rounded-pill fw-bold shadow-sm">
                Voir tout le catalogue <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center">
        <div class="mb-4">
            <h4 class="fw-bold">Pourquoi choisir AfroStyle ?</h4>
            <p class="text-muted fs-5">Nous nous engageons à vous offrir le meilleur de l'artisanat avec un service irréprochable.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-6">
                <i class="bi bi-patch-check-fill text-warning fs-1"></i>
                <h6 class="mt-3 fw-bold">Produits Authentiques</h6>
            </div>
            <div class="col-lg-3 col-6">
                <i class="bi bi-truck text-dark fs-1"></i>
                <h6 class="mt-3 fw-bold">Livraison Rapide</h6>
            </div>
            <div class="col-lg-3 col-6">
                <i class="bi bi-shield-lock-fill text-success fs-1"></i>
                <h6 class="mt-3 fw-bold">Paiement Sécurisé</h6>
            </div>
            <div class="col-lg-3 col-6">
                <i class="bi bi-heart-fill text-danger fs-1"></i>
                <h6 class="mt-3 fw-bold">Avis Vérifiés</h6>
            </div>
        </div>
    </div>
</section>

<?php include_once 'commun/footer.php'; ?>