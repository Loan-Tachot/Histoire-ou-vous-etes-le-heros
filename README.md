# Histoire où vous êtes le héros

Un jeu de rôle textuel PHP/MySQL développé dans le cadre d'un projet SLAM (1ère année). Le joueur crée un personnage, explore une histoire ramifiée, améliore ses statistiques et collecte des objets.

---

## 🛠 Structure du projet

```
/ (racine)
├── Looping3.lo1
├── Looping3.loo
├── README.md          # ce fichier
├── sql.txt            # schéma et instructions SQL
└── programe/          # code source PHP et CSS
    ├── choix.php
    ├── connexion.php
    ├── creer_personnage.php
    ├── index.php
    ├── inventaire.php
    ├── jeu.php
    └── css/
        └── style.css
```

## 🚀 Installation et configuration

1. **Serveur web** : Apache ou équivalent avec PHP 7+.
2. **Base de données** : MySQL/MariaDB.

### Création de la base

1. Se connecter au serveur MySQL : `mysql -u root -p`.
2. Créer la base et importer le schéma :
   ```sql
   CREATE DATABASE jeu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE jeu;
   SOURCE /chemin/vers/sql.txt;
   ```
3. Insérer des données initiales dans les tables `Classe`, `histoire`, etc.

### Configuration PHP

- Le fichier `programe/connexion.php` contient les identifiants de connexion ; adaptez `$host`, `$db`, `$user` et `$password` si nécessaire.
- Pour développer en local sans toucher au code existant, créez un fichier `config.local.php` à la racine du projet (il sera ignoré par Git) et définissez-y vos paramètres de base de données :
  ```php
  <?php
  $host = 'localhost';
  $db   = 'jeu';
  $user = 'root';
  $password = '';
  ```
  Le script lira ces valeurs avant d’utiliser les valeurs par défaut.
- Le projet ne nécessite aucune dépendance externe.

### Déploiement

- Placez le dossier `programe/` sous le répertoire accessible par votre serveur web. Par exemple : `C:/xampp/htdocs/Histoire-ou-vous-etes-le-heros/programe`.
- Placez si vous le souhaitez un fichier `accueil.php` à la racine (fournie) : il servira de page d'entrée et redirige automatiquement vers le jeu si nécessaire.
- Assurez-vous d'utiliser une URL incluant `programe`, par exemple : `http://localhost/Histoire-ou-vous-etes-le-heros/programe/index.php`.
- Les liens internes sont relatifs, il est donc important de conserver la structure des dossiers.

## 🧩 Fonctionnalités principales

- **Créer un personnage** : sélection d'une classe, création d'un enregistrement dans la table `personnage`.
- **Parcours de l'histoire** : affichage du texte courant (`jeu.php`), choix ramifiés stockés dans `Deboucher`.
- **Inventaire** : consultation des objets obtenus (`inventaire.php`).
- **Gestion de session** : le personnage est stocké dans `_SESSION['personnage']`.

## 💡 Astuces & dépannage

- **404 erreurs** : vérifiez que l'URL correspond bien à l'emplacement du dossier `programe` ou adaptez le chemin.
- **Erreurs SQL** : contrôlez que la base `jeu` contient bien les tables créées par `sql.txt`.
- **Sessions** : `session_start()` est appelé dans `connexion.php` ; toute page incluant ce fichier pourra lire/écrire la session.
- **Modifications de chemin** : pour éviter des problèmes d'inclusion, utilisez `include __DIR__ . '/connexion.php';` si vous déplacez des fichiers.

## 📦 Extensions futures possibles

- Interface d'administration pour éditer les histoires et les choix.
- Système de combat et de gain d'expérience.
- Sauvegarde automatique de l'état du personnage.

---

Ce projet est destiné à un usage éducatif. N'hésitez pas à l'adapter ou à le réutiliser pour vos propres aventures !

