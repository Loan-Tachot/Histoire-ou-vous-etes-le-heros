<?php

// possibilité de charger un fichier de configuration local sans toucher
// au code existant. Créez `config.local.php` à la racine du projet
// avec vos propres valeurs (ce fichier ne doit pas être commité).
if(file_exists(__DIR__ . '/../config.local.php')){
    include __DIR__ . '/../config.local.php';
}

// paramètres par défaut
$host     = $host     ?? 'localhost';
$db       = $db       ?? 'jeu';
$user     = $user     ?? 'root';
$password = $password ?? '';

// affichage des erreurs uniquement en local (domaine contenant "localhost")
if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

try{
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    die("Erreur : " . $e->getMessage());
}

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

?>