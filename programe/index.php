<?php
// redirige vers la page d'accueil principale si nécessaire
if(basename($_SERVER['PHP_SELF']) === 'index.php'){
    // si on est dans /programe/ on monte d'un niveau
    header('Location: ../accueil.php');
    exit;
}
include __DIR__ . "/connexion.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Jeu RPG</title>
<!-- base pour les liens relatifs en local -->
<base href="<?= dirname($_SERVER['SCRIPT_NAME']); ?>/">
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<h1>⚔️ Aventure RPG</h1>

<a class="btn" href="creer_personnage.php">Créer un personnage</a>

<?php
if(isset($_SESSION['personnage'])){
    echo "<a class='btn' href='jeu.php'>Continuer l'aventure</a>";
}
?>

</body>
</html>