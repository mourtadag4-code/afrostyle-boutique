<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Connexion et Header (On sécurise l'inclusion)
require_once "commun/connexiondb.php";
if (!isset($pdo) && isset($conn)) { $pdo = $conn; } // Au cas où ta variable s'appelle $conn

include_once "commun/header.php";

// 2. Vérification de la session
if (!isset($_SESSION['utilisateur_id'])) {
    echo "<script>window.location.href='connexion.php';</script>";
    exit;
}

$userId = $_SESSION['utilisateur_id'];

// 3. Récupération des données utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// 4. Statistiques (Commandes)
$commandes_count = 0;
try {
    $stmtCmd = $pdo->prepare("SELECT COUNT(*) as count FROM commande WHERE id_utilisateur = ?");
    $stmtCmd->execute([$userId]);
    $commandes_count = $stmtCmd->fetch(PDO::FETCH_ASSOC)['count'];
} catch (Exception $e) { $commandes_count = 0; }

// 5. Traitement de la mise à jour
$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $update = $pdo->prepare("UPDATE utilisateurs SET adresse = ?, telephone = ? WHERE id_utilisateur = ?");
    if($update->execute([$adresse, $telephone, $userId])) {
        $success = "Profil mis à jour !";
        // On rafraîchit les données
        $userData['adresse'] = $adresse;
        $userData['telephone'] = $telephone;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .account-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: 'Poppins', sans-serif; }
        .account-header { 
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); 
            color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; 
            display: flex; justify-content: space-between; align-items: center; 
        }
        .user-profile-large { display: flex; align-items: center; gap: 20px; }
        .avatar { 
            width: 80px; height: 80px; background: white; color: #8B4513; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            font-size: 32px; font-weight: bold; 
        }
        .user-email { font-size: 14px; background: rgba(255, 255, 255, 0.1); padding: 5px 10px; border-radius: 4px; }
        .stat-number { display: block; font-size: 32px; font-weight: bold; color: #FFD700; }
        
        .account-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .account-card { 
            background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); 
            overflow: hidden; border: none; padding: 20px; 
        }
        .card-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .card-header i { color: #8B4513; font-size: 20px; }
        
        .form-group { margin-bottom: 15px; }
        label { font-weight: 500; color: #555; display: block; margin-bottom: 5px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        
        .btn-afro { background: #8B4513; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
        .btn-outline-afro { border: 2px solid #8B4513; color: #8B4513; padding: 10px; border-radius: 8px; text-decoration: none; display: block; text-align: center; margin-top: 10px; }
        
        @media (max-width: 768px) { .account-header { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>

<main class="account-container">
    <?php if ($success): ?>
        <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?= $success ?></div>
    <?php endif; ?>

    <div class="account-header">
        <div class="user-profile-large">
            <div class="avatar"><?= strtoupper(substr($userData['nom'], 0, 1)) ?></div>
            <div class="user-info">
                <h2 class="mb-1"><?= htmlspecialchars($userData['nom']) ?></h2>
                <p class="user-email mb-0"><?= htmlspecialchars($userData['email']) ?></p>
            </div>
        </div>
        <div class="d-flex gap-4">
            <div class="text-center">
                <span class="stat-number"><?= $commandes_count ?></span>
                <small>Commandes</small>
            </div>
        </div>
    </div>

    <div class="account-grid">
        <div class="account-card">
            <div class="card-header">
                <i class="fas fa-user-edit"></i>
                <h3 class="h5 mb-0">Mon Profil</h3>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($userData['nom']) ?>" readonly style="background:#f8f9fa;">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($userData['telephone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Adresse de livraison</label>
                    <textarea name="adresse" class="form-control" rows="2"><?= htmlspecialchars($userData['adresse'] ?? '') ?></textarea>
                </div>
                <button type="submit" name="update_profile" class="btn-afro">Enregistrer</button>
            </form>
        </div>

        <div class="account-card">
            <div class="card-header">
                <i class="fas fa-shopping-bag"></i>
                <h3 class="h5 mb-0">Mes Activités</h3>
            </div>
            <p>Gérez vos commandes et vos préférences.</p>
            <a href="commandes.php" class="btn-outline-afro"><i class="fas fa-box me-2"></i> Historique commandes</a>
            <a href="panier.php" class="btn-outline-afro"><i class="fas fa-shopping-cart me-2"></i> Mon panier</a>
            <a href="deconnexion.php" class="btn-outline-afro" style="border-color:#dc3545; color:#dc3545;"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
        </div>
    </div>
</main>

<?php include_once "commun/footer.php"; ?>
</body>
</html>