<?php
session_start();
require_once 'commun/connexiondb.php';

// Sécurité : Si pas d'ID de commande, on redirige vers l'accueil
if (!isset($_GET['id'])) {
    header("Location: catalogue.php");
    exit();
}

$id_commande = (int)$_GET['id'];
$user_id = $_SESSION['utilisateur_id'] ?? 0; // Sécurité si session perdue

// On récupère les infos de la commande pour l'affichage
// Correction de la requête pour s'assurer de récupérer le montant_total
$stmt = $pdo->prepare("SELECT c.*, p.methode_paiement 
                       FROM commande c 
                       LEFT JOIN paiement p ON c.id_commande = p.id_commande 
                       WHERE c.id_commande = ? AND c.id_utilisateur = ?");
$stmt->execute([$id_commande, $user_id]);
$commande = $stmt->fetch();

// Si la commande n'existe pas pour cet utilisateur
if (!$commande) {
    header("Location: catalogue.php");
    exit();
}

// RÉPARATION DE L'ERREUR DEPRECATED : On prépare la variable avant l'affichage
$methode = $commande['methode_paiement'] ?? 'Non spécifié';

$page_title = "Commande Réussie - AfroStyle";
require_once 'commun/header.php';
?>

<style>
    .success-card {
        border-radius: 30px;
        border: none;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
    }
    .check-icon {
        width: 100px;
        height: 100px;
        background: #D4AF37;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        margin: -50px auto 20px;
        border: 8px solid white;
    }
    .step-box {
        background: #fdfaf3;
        border-left: 5px solid #D4AF37;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        text-align: left;
    }
</style>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            
            <div class="card success-card p-4 p-md-5 bg-white">
                <div class="check-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                
                <h1 class="fw-bold text-dark mt-3">MERCI POUR VOTRE ACHAT !</h1>
                <p class="text-muted lead">Votre commande a été enregistrée avec succès sous le numéro :</p>
                <div class="badge bg-dark fs-4 px-4 py-2 mb-4 rounded-pill">#<?= $id_commande ?></div>
                
                <div class="row g-3 mb-4">
                    <div class="col-6 text-start">
                        <small class="text-muted d-block">Montant réglé :</small>
                        <span class="fw-bold text-danger fs-5"><?= number_format($commande['montant_total'] ?? $commande['total_ttc'] ?? 0, 0, '', ' ') ?> FCFA</span>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block">Mode de paiement :</small>
                        <span class="fw-bold text-dark"><?= strtoupper(str_replace('_', ' ', $methode)) ?></span>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-4 text-uppercase" style="letter-spacing: 2px;">Prochaines étapes</h5>
                
                <div class="step-box">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone-outbound fs-4 me-3 text-warning"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Confirmation téléphonique</h6>
                            <p class="small text-muted mb-0">Un conseiller AfroStyle va vous appeler dans les 30 min pour confirmer l'adresse.</p>
                        </div>
                    </div>
                </div>

                <div class="step-box">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck fs-4 me-3 text-warning"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Expédition express</h6>
                            <p class="small text-muted mb-0">Votre colis est en cours de préparation dans nos ateliers.</p>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-center mt-5">
                    <a href="commandes.php" class="btn btn-outline-dark rounded-pill px-4 py-2">
                        <i class="bi bi-list-ul me-2"></i>Voir mes commandes
                    </a>
                    <a href="catalogue.php" class="btn btn-warning text-white fw-bold rounded-pill px-5 py-2 shadow-sm">
                        RETOUR À LA BOUTIQUE
                    </a>
                </div>
            </div>

            <p class="mt-4 text-muted small">
                Un e-mail de confirmation vient d'être envoyé à votre adresse. <br>
                Besoin d'aide ? Contactez notre support au **+221 78 106 03 85**.
            </p>
        </div>
    </div>
</div>

<?php require_once 'commun/footer.php'; ?>