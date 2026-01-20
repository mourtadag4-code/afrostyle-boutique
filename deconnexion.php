<?php
// 1. On ouvre la session pour pouvoir la manipuler
session_start();

// 2. On vide toutes les variables de session (ID, Nom, Panier, etc.)
$_SESSION = array();

// 3. On détruit la session sur le serveur
session_destroy();

// 4. On redirige vers l'accueil ou la page de connexion
header("Location: index.php");
exit();
?>