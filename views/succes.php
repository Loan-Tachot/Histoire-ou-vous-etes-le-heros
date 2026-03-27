<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Héros — Succès</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="card">
        <a class="back" href="index.php?page=jeu">← Retour au jeu</a>
        <h1>Succès</h1>
        <p class="hint">
            <?= count($succes) ?> / <?= $total ?> succès débloqués
        </p>

        <?php if (empty($succes)): ?>
        <p class="muted">Aucun succès pour l'instant. Explorez le monde !</p>
        <?php else: ?>
        <ul class="item-list">
            <?php foreach ($succes as $s): ?>
            <li>
                <strong><?= htmlspecialchars($s['Label']) ?></strong>
                <span><?= htmlspecialchars($s['Effet']) ?></span>
                <small class="muted">Débloqué le <?= htmlspecialchars($s['debloque_le']) ?></small>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </main>
</body>
</html>
