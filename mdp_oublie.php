<?php
session_start();
require_once 'commun/connexiondb.php';

$erreur = '';
$message = '';
$token = $_GET['token'] ?? null; 

// --- CAS 1 : RÉINITIALISATION (L'utilisateur a cliqué sur le lien avec token) ---
if ($token && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_mdp'])) {
    $mdp = $_POST['nouveau_mdp'];
    $mdp_confirm = $_POST['confirm_mdp'];

    $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE reset_token = ? AND reset_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        if (strlen($mdp) < 6) {
            $erreur = "Le mot de passe doit faire au moins 6 caractères.";
        } elseif ($mdp !== $mdp_confirm) {
            $erreur = "Les mots de passe ne correspondent pas.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ?, reset_token = NULL, reset_expires_at = NULL WHERE id_utilisateur = ?");
            $update->execute([$hash, $user['id_utilisateur']]);
            $message = "✅ Mot de passe modifié avec succès !";
            $token = null; 
        }
    } else {
        $erreur = "Ce lien de récupération est invalide ou a expiré.";
        $token = null; 
    }
}

// --- CAS 2 : DEMANDE INITIALE (Saisie de l'email) ---
if (!$token && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $newToken = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $update = $pdo->prepare("UPDATE utilisateurs SET reset_token = ?, reset_expires_at = ? WHERE email = ?");
        $update->execute([$newToken, $expiration, $email]);

        // On utilise le nom de fichier sans accent ici pour le lien
        $lien = "mdp_oublie.php?token=" . $newToken;
        $message = "Lien généré avec succès. <br> 
                   <a href='$lien' class='btn btn-warning btn-sm mt-3 shadow-sm'>Simuler l'Email : Changer mon mot de passe</a>";
    } else {
        $erreur = "Aucun compte n'est enregistré avec cet email.";
    }
}

$page_title = "Récupération - AFROSTYLE";
require_once 'commun/header.php';
?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0 fw-bold" style="color: #D4AF37;">AFROSTYLE</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php if ($erreur): ?>
                        <div class="alert alert-danger border-0 small shadow-sm"><?= $erreur ?></div>
                    <?php endif; ?>

                    <?php if ($message): ?>
                        <div class="alert alert-success border-0 small shadow-sm text-center">
                            <?= $message ?>
                            <?php if(strpos($message, 'succès') !== false): ?>
                                <br><a href="connexion.php" class="btn btn-dark btn-sm mt-3 rounded-pill">Se connecter</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($token && !$message): ?>
                        <h5 class="fw-bold mb-3">Nouveau mot de passe</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="small fw-bold">Nouveau mot de passe</label>
                                <input type="password" name="nouveau_mdp" class="form-control rounded-pill" required>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold">Confirmer</label>
                                <input type="password" name="confirm_mdp" class="form-control rounded-pill" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-2 shadow-sm">VALIDER</button>
                        </form>

                    <?php elseif (!$message || $erreur): ?>
                        <h5 class="fw-bold mb-3 text-center">Mot de passe oublié ?</h5>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="small fw-bold">Votre Email</label>
                                <input type="email" name="email" class="form-control rounded-pill" placeholder="exemple@afrostyle.com" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-2 shadow-sm">RECEVOIR LE LIEN</button>
                        </form>
                    <?php endif; ?>

                    <hr class="my-4">
                    <div class="text-center">
                        <a href="connexion.php" class="text-muted small text-decoration-none">← Retour à la connexion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'commun/footer.php'; ?>