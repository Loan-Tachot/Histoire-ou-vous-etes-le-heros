<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Héros — Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="page-accueil">
    <div class="hero-card">
        <span class="rune-symbol">⚔</span>
        <h1>Histoire où vous êtes le héros</h1>
        <p class="subtitle">Un jeu de rôle textuel dans un monde de fantasy épique.<br>Forgez votre légende.</p>
        <nav>
            <a class="btn" href="index.php?page=creer_personnage">Nouvelle aventure</a>
            <?php if (isset($_SESSION['perso_nom'])): ?>
            <a class="btn btn-sec" href="index.php?page=jeu">Continuer</a>
            <a class="btn btn-ghost" href="index.php?page=succes">Succès</a>
            <?php endif; ?>
            <a class="btn btn-ghost" href="presentation.html">Le monde</a>
        </nav>
    </div>
    <?php if (!empty($GLOBALS['db_error'])): ?>
    <div class="alert"><?= htmlspecialchars($GLOBALS['db_error']) ?></div>
    <?php endif; ?>
</body>
</html>
