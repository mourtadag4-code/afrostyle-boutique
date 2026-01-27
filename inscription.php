<?php
session_start();
require_once 'commun/connexiondb.php'; 

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération sécurisée (on vérifie si la clé existe avant le trim)
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : "";
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : "";
    $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : "";
    $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $mot_de_passe = $_POST['mot_de_passe'] ?? "";

    if (empty($email) || empty($mot_de_passe)) {
        $error = "Veuillez remplir les champs obligatoires.";
    } else {
        // 1. Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Cet email est déjà lié à un compte.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            try {
                $sql = "INSERT INTO utilisateurs (nom, prenom, adresse, email, telephone, role, mot_de_passe, date_inscription) 
                        VALUES (:nom, :prenom, :adresse, :email, :tel, 'client', :mdp, NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'nom'     => $nom,
                    'prenom'  => $prenom,
                    'adresse' => $adresse,
                    'email'   => $email,
                    'tel'     => $telephone,
                    'mdp'     => $hash
                ]);

                $success = "Compte créé ! Bienvenue dans la famille AFROSTYLE. <a href='connexion.php' class='fw-bold text-decoration-none'>Connectez-vous ici</a>.";
            } catch (PDOException $e) {
                $error = "Erreur technique : " . $e->getMessage();
            }
        }
    }
}

$page_title = "Inscription - AFROSTYLE";
include_once 'commun/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<main class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-4 bg-dark d-none d-md-flex align-items-center justify-content-center text-white p-4">
                            <div class="text-center">
                                <i class="fas fa-user-plus fa-4x mb-3 text-warning"></i>
                                <h3 class="fw-bold">Bienvenue !</h3>
                                <p class="small text-light">Créez votre compte pour suivre vos commandes et accéder à nos exclusivités.</p>
                            </div>
                        </div>

                        <div class="col-md-8 bg-white p-4 p-md-5">
                            <h2 class="fw-bold mb-4">Inscription</h2>

                            <?php if ($error): ?>
                                <div class="alert alert-danger border-0 small"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success border-0 small"><i class="fas fa-check-circle me-2"></i><?= $success ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Identité</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small">Nom</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                                            <input type="text" name="nom" class="form-control border-start-0 ps-0" placeholder="Diop" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small">Prénom</label>
                                        <input type="text" name="prenom" class="form-control" placeholder="Fatou" required>
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase small fw-bold mt-4 mb-3 border-bottom pb-2">Coordonnées</h6>
                                <div class="mb-3">
                                    <label class="form-label small">Adresse de livraison</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        <input type="text" name="adresse" class="form-control border-start-0 ps-0" placeholder="Ex: Rue 10, Dakar" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7 mb-3">
                                        <label class="form-label small">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="fatou@gmail.com" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label small">Téléphone</label>
                                        <input type="tel" name="telephone" class="form-control" placeholder="+221..." required>
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase small fw-bold mt-4 mb-3 border-bottom pb-2">Sécurité</h6>
                                <div class="mb-4">
                                    <label class="form-label small">Choisir un mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="mot_de_passe" class="form-control border-start-0 ps-0" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-uppercase shadow-sm">
                                    Créer mon compte client
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once 'commun/footer.php'; ?>