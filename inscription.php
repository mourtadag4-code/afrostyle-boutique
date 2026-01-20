<?php
session_start();
require_once 'commun/connexiondb.php'; // On utilise ton fichier centralisé

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $adresse = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $sexe = $_POST['sexe'] ?? 'M'; // Récupère le sexe du formulaire

    // 1. Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $error = "Cet email est déjà utilisé par un autre compte.";
    } else {
        // 2. Hachage sécurisé du mot de passe
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // 3. Insertion dans la table utilisateurs
        try {
            $sql = "INSERT INTO utilisateurs (nom, prenom, adresse, email, telephone, sexe, mot_de_passe, role) 
                    VALUES (:nom, :prenom, :adresse, :email, :tel, :sexe, :mdp, 'client')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nom'     => $nom,
                'prenom'  => $prenom,
                'adresse' => $adresse,
                'email'   => $email,
                'tel'     => $telephone,
                'sexe'    => $sexe,
                'mdp'     => $hash
            ]);

            $success = "Félicitations ! Votre compte a été créé. <a href='connexion.php' class='alert-link'>Connectez-vous ici</a>.";
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}

$page_title = "Créer un compte - AFROSTYLE";
require_once 'commun/header.php';
?>

<main class="bg-light py-5" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Inscription</h2>
                            <p class="text-muted">Rejoignez la communauté AFROSTYLE</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger rounded-pill px-4 small"><?= $error ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success rounded-4 px-4 small"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nom</label>
                                    <input type="text" name="nom" class="form-control rounded-pill" placeholder="Ex: Diop" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Prénom</label>
                                    <input type="text" name="prenom" class="form-control rounded-pill" placeholder="Ex: Fatou" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Sexe</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexe" value="M" checked>
                                        <label class="form-check-label">Homme</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexe" value="F">
                                        <label class="form-check-label">Femme</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Adresse complète</label>
                                <input type="text" name="adresse" class="form-control rounded-pill" placeholder="Rue, Ville, Quartier" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control rounded-pill" placeholder="+221 ..." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control rounded-pill" placeholder="nom@exemple.com" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Mot de passe</label>
                                <input type="password" name="mot_de_passe" class="form-control rounded-pill" placeholder="********" required>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold shadow-sm">
                                CRÉER MON COMPTE
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="small mb-0">Vous avez déjà un compte ? <a href="connexion.php" class="text-dark fw-bold">Se connecter</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'commun/footer.php'; ?>