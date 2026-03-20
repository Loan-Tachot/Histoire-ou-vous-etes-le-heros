<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Héros — Inventaire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="card">
        <a class="back" href="index.php?page=jeu">← Retour au jeu</a>
        <h1>Inventaire</h1>

        <?php if (empty($objets)): ?>
        <p class="muted">Votre sac est vide. Explorez le monde pour trouver des objets.</p>
        <?php else: ?>
        <ul class="item-list">
            <?php foreach ($objets as $o): ?>
            <li>
                <strong><?= htmlspecialchars($o['Label']) ?></strong>
                <span><?= htmlspecialchars($o['Effet']) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </main>
</body>
</html>
