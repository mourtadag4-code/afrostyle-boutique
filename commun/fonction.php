<?php
function getImagePath($image_name) {
    $base_path = '/AfroStyle/public/Images/';
    $default_image = 'default.jpg';
    
    if (empty($image_name)) {
        return $base_path . $default_image;
    }
    
    $local_path = $_SERVER['DOCUMENT_ROOT'] . $base_path . $image_name;
    
    // Vérifier si le fichier existe
    if (file_exists($local_path)) {
        return $base_path . $image_name;
    }
    
    return $base_path . $default_image;
}
?>