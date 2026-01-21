<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'commun/connexiondb.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Veuillez remplir tous les champs";
    } else {
        $sql = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();
        
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // --- 1. MISE EN SESSION DE L'UTILISATEUR ---
            $id_user = $utilisateur['id_utilisateur'];
            $_SESSION['utilisateur_id'] = $id_user;
            $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];
            $_SESSION['utilisateur_nom'] = $utilisateur['prenom'] . ' ' . $utilisateur['nom'];
            $_SESSION['utilisateur_role'] = $utilisateur['role'];

            // --- 2. RÉCUPÉRATION DU PANIER DEPUIS LA BDD ---
            $_SESSION['panier'] = []; 
            $stmtPanier = $pdo->prepare("SELECT p.id_produit, p.nom_produit, p.prix_unitaire, p.image_produit, pan.quantite 
                                         FROM panier pan 
                                         JOIN produit p ON pan.id_produit = p.id_produit 
                                         WHERE pan.id_utilisateur = ?");
            $stmtPanier->execute([$id_user]);
            while ($row = $stmtPanier->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['panier'][$row['id_produit']] = [
                    'nom' => $row['nom_produit'],
                    'prix' => $row['prix_unitaire'],
                    'image' => $row['image_produit'],
                    'quantite' => $row['quantite']
                ];
            }

            // --- 3. RÉCUPÉRATION DES FAVORIS DEPUIS LA BDD ---
            $_SESSION['favoris'] = []; 
            $stmtFav = $pdo->prepare("SELECT id_produit FROM favoris WHERE id_utilisateur = ?");
            $stmtFav->execute([$id_user]);
            while ($row = $stmtFav->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['favoris'][$row['id_produit']] = true;
            }

            // --- 4. REDIRECTION ---
            if (isset($_GET['redirect'])) {
                $url = $_GET['redirect'];
            } elseif (isset($_SESSION['redirect_to'])) {
                $url = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
            } else {
                $url = 'index.php';
            }

            header("Location: " . $url);
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect";
        }
    }
}

$page_title = "Connexion - AFROSTYLE";
require_once 'commun/header.php';
?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h3 class="text-center fw-bold mb-4">Connexion</h3>

                    <?php if ($erreur): ?>
                        <div class="alert alert-danger small border-0"><?= $erreur ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control rounded-pill" name="email" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Mot de passe</label>
                            <input type="password" class="form-control rounded-pill" name="mot_de_passe" required>
                        </div>
                        
                        <div class="text-end mb-4">
                            <a href="mdp_oublie.php" class="text-muted small text-decoration-none italic">Mot de passe oublié ?</a>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold mb-3 shadow-sm">
                            SE CONNECTER
                        </button>
                        <p class="text-center small mb-0">Nouveau ? <a href="inscription.php" class="text-dark fw-bold">Créer un compte</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'commun/footer.php'; ?>