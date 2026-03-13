<!DOCTYPE html>
<html>
<head>
<title>Jeu RPG</title>
<!-- base pour les liens relatifs en local -->
<base href="<?= $base ?>">
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<h1>⚔️ Aventure RPG</h1>

<a class="btn" href="index.php?page=creer_personnage">Créer un personnage</a>

<?php if($personnage): ?>
<a class="btn" href="jeu.php">Continuer l'aventure</a>
<h2>Statistiques</h2>
<ul>
<?php foreach($stats as $key => $value): ?>
<li><?= $key ?> : <?= $value ?></li>
<?php endforeach; ?>
</ul><?php endif; ?>

</body>
</html>