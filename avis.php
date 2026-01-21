<?php
session_start();
require_once 'commun/connexiondb.php';

// --- Vérification si l’utilisateur est connecté ---
if (!isset($_SESSION['utilisateur_id'])) {
    $redirect_url = "avis.php" . (isset($_GET['id_produit']) ? "?id_produit=".$_GET['id_produit'] : "");
    $_SESSION['redirect_to'] = $redirect_url; 
    header("Location: connexion.php");
    exit();
}
// --- Préparation des variables utilisateur et produit---
$id_user = $_SESSION['utilisateur_id'];
$message_success = "";
// On récupère l'id du produit si on vient du catalogue
$id_produit_selectionne = isset($_GET['id_produit']) ? (int)$_GET['id_produit'] : 0;

// --- TRAITEMENT DU FORMULAIRE D'AVIS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publier_avis'])) {
    $id_produit = (int)$_POST['id_produit'];
    $note = (int)$_POST['note'];
    $commentaire = trim($_POST['commentaire']);

    if ($id_produit > 0 && !empty($commentaire)) {
        // Attention : vérifie si ta colonne s'appelle 'commentaires' ou 'commentaire' dans ta base
        $ins = $pdo->prepare("INSERT INTO avis (id_produit, id_utilisateur, note, commentaires, date_avis) VALUES (?, ?, ?, ?, NOW())");
        $ins->execute([$id_produit, $id_user, $note, $commentaire]);
        $message_success = "Merci ! Votre avis a été publié avec succès.";
    }
}

// Récupérer les produits pour le sélecteur
$produits = $pdo->query("SELECT id_produit, nom_produit FROM produit ORDER BY nom_produit")->fetchAll();

// Récupérer tous les avis
$tous_les_avis = $pdo->query("SELECT a.*, p.nom_produit, u.prenom, u.nom FROM avis a 
                               JOIN produit p ON a.id_produit = p.id_produit 
                               JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
                               ORDER BY a.date_avis DESC LIMIT 10")->fetchAll();

$page_title = "Avis Clients - AFROSTYLE";
include_once 'commun/header.php';
?>

<style>
    .hero-header { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('public/Images/banner.jpg') center/cover; color: white; padding: 60px 0; }
    .avis-card { transition: 0.3s; border-left: 5px solid #D4AF37 !important; }
    .avis-card:hover { transform: translateX(5px); }
</style>

<div class="hero-header text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold text-uppercase">Avis Clients</h1>
        <p class="lead">Votre satisfaction est notre plus belle récompense</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow p-4 rounded-4 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-3 text-dark">Partagez votre expérience</h4>
                
                <?php if ($message_success): ?>
                    <div class="alert alert-success border-0 shadow-sm small"><?= $message_success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold">Modèle acheté</label>
                        <select name="id_produit" class="form-select border-0 bg-light" required>
                            <option value="">Choisir un produit...</option>
                            <?php foreach($produits as $pr): ?>
                                <option value="<?= $pr['id_produit'] ?>" <?= ($id_produit_selectionne == $pr['id_produit']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pr['nom_produit']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Votre note</label>
                        <select name="note" class="form-select border-0 bg-light text-warning fw-bold">
                            <option value="5">★★★★★ (Excellent)</option>
                            <option value="4">★★★★☆ (Très bien)</option>
                            <option value="3">★★★☆☆ (Moyen)</option>
                            <option value="2">★★☆☆☆ (Déçu)</option>
                            <option value="1">★☆☆☆☆ (Mauvais)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Commentaire</label>
                        <textarea name="commentaire" class="form-control border-0 bg-light" rows="4" placeholder="Dites-nous tout..." required></textarea>
                    </div>
                    <button type="submit" name="publier_avis" class="btn btn-warning w-100 rounded-pill fw-bold shadow-sm">PUBLIER L'AVIS</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <h4 class="fw-bold mb-4">Derniers témoignages</h4>
            <?php if (empty($tous_les_avis)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-dots text-muted display-1"></i>
                    <p class="text-muted mt-3">Aucun avis pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach($tous_les_avis as $av): ?>
                    <div class="card border-0 shadow-sm mb-3 p-4 rounded-4 avis-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($av['prenom'] . ' ' . $av['nom']) ?></h6>
                                <div class="text-warning my-1">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $av['note']) ? '★' : '☆'; ?>
                                </div>
                                <span class="badge rounded-pill bg-warning text-dark px-3" style="font-size: 0.7rem;">Produit : <?= htmlspecialchars($av['nom_produit']) ?></span>
                            </div>
                            <small class="text-muted fw-bold"><?= date('d/m/Y', strtotime($av['date_avis'])) ?></small>
                        </div>
                        <p class="mt-3 text-secondary italic mb-0">" <?= nl2br(htmlspecialchars($av['commentaires'])) ?> "</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'commun/footer.php'; ?>