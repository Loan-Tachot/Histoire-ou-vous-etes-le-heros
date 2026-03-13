<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Accueil - Histoire où vous êtes le héros</title>
    <link rel="stylesheet" href="programe/css/style.css">
</head>
<body class="accueil-page">
    <div class="hero">
        <h1>Bienvenue dans l'univers "Histoire où vous êtes le héros"</h1>
        <p>Ce petit jeu de rôle textuel vous permet de créer un personnage et de partir à l'aventure dans un monde de fantasy épique.</p>
    </div>

    <nav class="main-nav">
        <ul class="nav-list">
            <li><a class="btn" href="programe/index.php?page=index">Jouer</a></li>
            <li><a class="btn" href="programe/index.php?page=creer_personnage">Créer un personnage</a></li>
            <li><a class="btn" href="presentation.html">Présentation du thème fantastique</a></li>
            <li><a class="btn" href="README.md">Documentation</a></li>
        </ul>
        <button class="btn fullscreen-btn" id="fullscreenBtn" onclick="toggleFullscreen()">Plein écran</button>
    </nav>

    <script>
        const fullscreenBtn = document.getElementById('fullscreenBtn');

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                enterFullscreen();
            } else {
                exitFullscreen();
            }
        }

        function enterFullscreen() {
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) { // Firefox
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari, Opera
                document.documentElement.webkitRequestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
                document.documentElement.msRequestFullscreen();
            }
        }

        function exitFullscreen() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari, Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', () => {
            if (document.fullscreenElement) {
                fullscreenBtn.textContent = 'Quitter plein écran';
            } else {
                fullscreenBtn.textContent = 'Plein écran';
            }
        });
    </script>