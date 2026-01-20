<?php
require_once 'auth_check.php';
require_once '../commun/connexiondb.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Supprimer l'image du dossier public/Images/
    $stmt = $pdo->prepare("SELECT image_produit FROM produit WHERE id_produit = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists("../" . $img)) {
        unlink("../" . $img);
    }

    // Supprimer de la base
    $pdo->prepare("DELETE FROM produit WHERE id_produit = ?")->execute([$id]);
}

header('Location: produits.php');
exit();