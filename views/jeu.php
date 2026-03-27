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
        <span class="stat" title="Points de vie">❤ <?= $stats['PV'] ?></span>
        <span class="stat" title="Force">💪 <?= $stats['Force'] ?></span>
        <span class="stat" title="Agilité">🌀 <?= $stats['Agilite'] ?></span>
        <span class="stat" title="Points de magie">✨ <?= $stats['PM'] ?></span>
        <span class="stat" title="Puissance">⚡ <?= $stats['Puissance'] ?></span>
        <span class="stat" title="Argent">🪙 <?= $stats['Argent'] ?></span>
        <nav class="topnav">
            <a href="index.php?page=succes">Succès</a>
            <a href="index.php?page=accueil">Menu</a>
        </nav>
    </header>

    <?php if (!empty($gainStats)): ?>
    <div class="gains">
        <?php foreach ($gainStats as $cle => $delta): ?>
        <span class="gain <?= $delta > 0 ? 'gain-pos' : 'gain-neg' ?>">
            <?= $delta > 0 ? '+' : '' ?><?= $delta ?>
            <?php
                $icones = array('PV'=>'❤','Force'=>'💪','Agilite'=>'🌀','PM'=>'✨','Puissance'=>'⚡','Argent'=>'🪙');
                echo isset($icones[$cle]) ? $icones[$cle] : $cle;
            ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($nouveauxSucces)): ?>
    <div class="succes-notif" id="succes-notif">
        <?php foreach ($nouveauxSucces as $s): ?>
        <div class="succes-item">
            <span class="succes-icon">🏆</span>
            <div>
                <strong>Succès débloqué !</strong>
                <span><?= htmlspecialchars($s['Label']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
        setTimeout(function() {
            var notif = document.getElementById('succes-notif');
            if (notif) notif.classList.add('succes-notif-hide');
        }, 4000);
    </script>
    <?php endif; ?>

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
        <a class="btn btn-sec" href="index.php?page=accueil">Retour au menu</a>
        <a class="btn" href="index.php?page=jeu&reset=1">Nouvelle partie</a>
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

    <script>
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function() {
        history.pushState(null, '', location.href);
        window.location.href = 'index.php?page=accueil';
    });
    </script>
</body>
</html>
