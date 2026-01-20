<?php
session_start();
require_once '../commun/connexiondb.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $mdp = $_POST['mdp'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND role = 'administrateur'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit();
    } else {
        $erreur = "Identifiants incorrects ou accès non autorisé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styleadmin.css">
</head>
<body style="background:#2c3e50; display:flex; align-items:center; height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 bg-white p-4 rounded-4 shadow">
                <h3 class="text-center fw-bold mb-4">AFROSTYLE ADMIN</h3>
                <?php if($erreur): ?> <div class="alert alert-danger small"><?= $erreur ?></div> <?php endif; ?>
                <form method="POST">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="mdp" class="form-control mb-3" placeholder="Mot de passe" required>
                    <button type="submit" class="btn btn-dark w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>