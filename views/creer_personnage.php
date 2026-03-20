<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Héros — Créer un personnage</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="card">
        <a class="back" href="index.php?page=accueil">← Retour</a>
        <h1>Forger votre héros</h1>

        <?php if (isset($_GET['err'])): ?>
        <p class="alert">
            <?= $_GET['err'] === 'db' ? 'Base de données indisponible.' : 'Le nom ne peut pas être vide.' ?>
        </p>
        <?php endif; ?>

        <form method="post" action="index.php?page=save_personnage">
            <label for="nom">Nom du personnage</label>
            <input type="text" id="nom" name="nom" required
                   placeholder="Entrez le nom de votre héros" autofocus>
            <button type="submit">Commencer l'aventure →</button>
        </form>

        <p class="hint">Classe de départ : <strong>Guerrier</strong> — vous pourrez évoluer en jeu.</p>
    </main>
</body>
</html>
