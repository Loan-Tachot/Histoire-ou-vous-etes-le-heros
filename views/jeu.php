<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Héros — Aventure</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <span class="perso-name">⚔ <?= htmlspecialchars($nom) ?></span>
        <span class="stat">❤ <?= $stats['PV'] ?></span>
        <span class="stat">💪 <?= $stats['Force'] ?></span>
        <span class="stat">🌀 <?= $stats['Agilite'] ?></span>
        <span class="stat">✨ <?= $stats['PM'] ?></span>
        <span class="stat">⚡ <?= $stats['Puissance'] ?></span>
        <span class="stat">🪙 <?= $stats['Argent'] ?></span>
        <nav class="topnav">
            <a href="index.php?page=inventaire">Inventaire</a>
            <a href="index.php?page=accueil">Menu</a>
        </nav>
    </header>

    <main class="card story-card">
        <?php if ($histoire): ?>
            <p class="story-text"><?= $texte ?></p>
        <?php else: ?>
            <p class="story-text muted">Aucune histoire trouvée.</p>
        <?php endif; ?>

        <?php if ($choix): ?>
        <div class="choices">
            <?php foreach ($choix as $c): ?>
            <a class="btn" href="index.php?page=choix&id=<?= $c['Id_Deboucher'] ?>">
                <?= htmlspecialchars($c['choix']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="fin">— Fin de ce chemin —</p>
        <a class="btn btn-sec" href="index.php?page=accueil">Recommencer</a>
        <?php endif; ?>
    </main>

    <?php if (!empty($_SESSION['chemins'])): ?>
    <details class="chemins">
        <summary>Chemins parcourus (<?= count($_SESSION['chemins']) ?>)</summary>
        <ul>
            <?php foreach ($_SESSION['chemins'] as $c): ?>
            <li>Histoire #<?= $c ?></li>
            <?php endforeach; ?>
        </ul>
    </details>
    <?php endif; ?>
</body>
</html>
