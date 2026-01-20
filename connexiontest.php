<?php
// Paramètres de connexion à MySQL
$host = "localhost";          // normalement localhost
$dbname = "afrostyle_shop";   // nom de ta base
$user = "root";               // utilisateur MySQL (souvent 'root')
$password = "";               // mot de passe (souvent vide sur Wamp/XAMPP)

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);

    // Gestion des erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test simple : compter le nombre de produits
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM produit");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Connexion réussie ! Il y a " . $result['total'] . " produits dans la base.";

} catch (PDOException $e) {
    // Si la connexion échoue, afficher l'erreur
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
