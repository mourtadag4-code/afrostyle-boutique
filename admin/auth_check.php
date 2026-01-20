<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On vérifie si l'ID existe en session et si le rôle est bien 'administrateur'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: login_admin.php');
    exit();
}
?>