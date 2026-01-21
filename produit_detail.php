<?php

session_start();

require_once 'commun/connexiondb.php';



// 1. Configuration PDO

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);



// 2. Récupération de l'ID

$id_produit = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$today = date('Y-m-d'); // Important pour vérifier si la promo est valide aujourd'hui



if ($id_produit <= 0) {

    header("Location: catalogue.php");

    exit;

}



// 3. Récupération du produit AVEC sa promotion (si elle existe)

// On utilise LEFT JOIN pour ne pas cacher le produit s'il n'a pas de promo

$stmt = $pdo->prepare("

    SELECT p.*, prom.pourcentage_reduction 

    FROM produit p 

    LEFT JOIN promotion prom ON p.id_produit = prom.id_produit 

    AND ? BETWEEN prom.date_debut AND prom.date_fin 

    WHERE p.id_produit = ?");

$stmt->execute([$today, $id_produit]);

$produit = $stmt->fetch();



if (!$produit) {

    die("Produit introuvable.");

}



// 4. Récupération des produits similaires

$stmtSim = $pdo->prepare("SELECT * FROM produit WHERE id_categorie = ? AND id_produit != ? LIMIT 4");

$stmtSim->execute([$produit['id_categorie'], $id_produit]);

$similaires = $stmtSim->fetchAll();



// --- CALCUL DES VARIABLES D'AFFICHAGE ---

$nom = $produit['nom_produit'];

$prix_regulier = $produit['prix_unitaire'];

$reduc = $produit['pourcentage_reduction'] ?? 0; // Si pas de promo, vaut 0



// Calcul du prix promo si une réduction existe

$prix_promo = ($reduc > 0) ? ($prix_regulier * (1 - $reduc / 100)) : 0;



$desc = $produit['description_produit'];

$img = !empty($produit['image_produit']) ? $produit['image_produit'] : 'public/Images/default.jpg';

$stock = (int)$produit['quantite_stock'];



require_once 'commun/header.php';

?>



<div class="container py-5">

    <div class="row mb-5">

        <div class="col-md-6 mb-4">

            <div class="position-relative">

                <?php if ($prix_promo > 0): ?>

                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow">

                        PROMO -<?= (int)$reduc ?>%

                    </span>

                <?php endif; ?>

                <img src="<?= $img ?>" class="img-fluid rounded-4 shadow-sm border" alt="<?= htmlspecialchars($nom) ?>">

            </div>

        </div>



        <div class="col-md-6">

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="catalogue.php" class="text-warning text-decoration-none">Catalogue</a></li>

                    <li class="breadcrumb-item active">Détails</li>

                </ol>

            </nav>



            <h1 class="fw-bold display-6"><?= htmlspecialchars($nom) ?></h1>

            

            <div class="my-4">

                <?php if ($prix_promo > 0): ?>

                    <span class="text-muted text-decoration-line-through fs-5"><?= number_format($prix_regulier, 0, '', ' ') ?> FCFA</span>

                    <h2 class="text-danger fw-bold"><?= number_format($prix_promo, 0, '', ' ') ?> FCFA</h2>

                <?php else: ?>

                    <h2 class="text-warning fw-bold"><?= number_format($prix_regulier, 0, '', ' ') ?> FCFA</h2>

                <?php endif; ?>

            </div>



            <p class="text-muted fs-5"><?= nl2br(htmlspecialchars($desc)) ?></p>

            

            <div class="p-3 bg-light rounded-3 my-4 border-start border-warning border-4">

                <strong>Disponibilité :</strong> 

                <?php if($stock > 0): ?>

                    <span class="text-success fw-bold">En stock (<?= $stock ?> articles restants)</span>

                <?php else: ?>

                    <span class="text-danger fw-bold">Rupture de stock</span>

                <?php endif; ?>

            </div>



            <?php if($stock > 0): ?>

            <form action="panier.php?action=add&id=<?= $id_produit ?>" method="POST" class="d-flex gap-2 mt-4">

                <input type="number" name="quantite" value="1" min="1" max="<?= $stock ?>" class="form-control" style="width: 100px;">

                <button type="submit" class="btn btn-warning btn-lg px-5 fw-bold shadow-sm">AJOUTER AU PANIER</button>

            </form>

            <?php endif; ?>

            

            <div class="mt-4">

                <a href="catalogue.php" class="text-decoration-none text-secondary">← Retourner au catalogue</a>

            </div>

        </div>

    </div>



    <?php if (!empty($similaires)): ?>

    <hr class="my-5">

    <h3 class="fw-bold mb-4">Vous aimerez aussi...</h3>

    <div class="row g-4">

        <?php foreach ($similaires as $sim): ?>

        <div class="col-6 col-md-3">

            <div class="card h-100 border-0 shadow-sm text-center p-2">

                <a href="produit_detail.php?id=<?= $sim['id_produit'] ?>">

                    <img src="<?= $sim['image_produit'] ?>" class="card-img-top rounded" style="height: 150px; object-fit: contain;">

                </a>

                <div class="card-body px-1">

                    <h6 class="text-truncate fw-bold mb-1"><?= htmlspecialchars($sim['nom_produit']) ?></h6>

                    <p class="text-warning mb-0 small fw-bold"><?= number_format($sim['prix_unitaire'], 0, '', ' ') ?> FCFA</p>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</div>



<?php require_once 'commun/footer.php'; ?>