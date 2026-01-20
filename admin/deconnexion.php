<?php
session_start();
session_unset();
session_destroy();

// Redirige vers le login admin qui est dans le même dossier
header("Location: login_admin.php");
exit();
?>
