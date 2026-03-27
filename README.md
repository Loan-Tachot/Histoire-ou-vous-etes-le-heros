# Histoire où vous êtes le héros

Un jeu de rôle textuel PHP/MariaDB développé dans le cadre d'un projet SLAM (BTS SIO 1ère année). Le joueur crée un personnage, explore une histoire ramifiée, améliore ses statistiques et collecte des objets.

---

## 🛠 Structure du projet

```
/ (racine)
├── .gitignore
├── README.md                  # ce fichier
├── sql.txt                    # schéma et données SQL
├── histoire_heros.pdf         # cahier des charges
├── models.php                 # accès BDD + logique métier
├── controllers.php            # contrôleurs MVC
├── index.php                  # point d'entrée, routeur
├── config.local.php           # (optionnel, ignoré par Git) config locale
└── views/                     # vues PHP
    ├── accueil.php
    ├── creer_personnage.php
    ├── jeu.php
    ├── inventaire.php
    ├── presentation.html
    └── css/
        └── style.css
```

## 🚀 Installation et configuration

1. **Serveur web** : Apache ou équivalent avec PHP 7+.
2. **Base de données** : MariaDB ou MySQL.

### Création de la base

1. Se connecter au serveur MySQL : `mysql -u root -p`.
2. Créer la base et importer le schéma :
   ```sql
   SOURCE /chemin/vers/sql.txt;
   ```
   Le fichier `sql.txt` crée automatiquement la base `Story`, toutes les tables et insère les données.

### Configuration PHP

- La connexion BDD est définie dans `models.php` avec les valeurs par défaut suivantes :
  - hôte : `192.168.56.200`
  - base : `Story`
  - utilisateur : `operateur`
  - mot de passe : `Bonk`
- Pour développer en local sans modifier le code, créez un fichier `config.local.php` à la racine (ignoré par Git) :
  ```php
  <?php
  $host = 'localhost';
  $db   = 'Story';
  $user = 'root';
  $pass = '';
  ```
- Le projet ne nécessite aucune dépendance externe.

### Déploiement

- Placez les fichiers à la racine du répertoire accessible par votre serveur web.
- Point d'entrée : `index.php` (ex : `http://localhost/histoire/index.php`).
- Les liens internes sont relatifs, conservez la structure des dossiers.

---

## 🧩 Fonctionnalités principales

- **Créer un personnage** : saisie du nom, initialisation des stats de base et de la session.
- **Parcours de l'histoire** : affichage du texte courant, choix ramifiés filtrés selon les stats du joueur.
- **Statistiques évolutives** : PV, Force, Agilité, PM, Puissance, Argent — recalculées à chaque nouveau nœud visité.
- **Réinitialisation** : les stats et chemins parcourus sont remis à zéro à chaque retour à la phase de sommeil (histoires 1 et 2).
- **Mort par excès de sommeil** : après 20 sommeils consécutifs, le joueur meurt (histoire 3).
- **Inventaire** : consultation des objets collectés au fil de l'aventure.
- **Conditions sur les choix** : certains choix ne s'affichent que si les stats du joueur satisfont une condition (`Force>=8`, `PM>Force`, etc.).
- **Blocage du retour arrière** : le bouton précédent du navigateur redirige vers l'accueil.
- **Gestion de session** : nom, stats, chemins et compteur de sommeil sont stockés en session PHP.

---

## 🗄 Structure de la base de données

| Table | Rôle |
|---|---|
| `histoire` | Nœuds narratifs (texte + amélioration associée) |
| `Deboucher` | Transitions entre nœuds (choix + condition requise) |
| `Amelioration` | Bonus de stats accordés par chaque nœud |
| `Objet` | Objets collectables |
| `obtenue` | Association objet ↔ nœud |
| `Classe` | Classes de personnage disponibles |

---

## 💡 Astuces & dépannage

- **Erreur de connexion BDD** : un message d'alerte s'affiche sur la page d'accueil si la base est inaccessible. Vérifiez les paramètres dans `models.php` ou créez un `config.local.php`.
- **Erreurs SQL** : vérifiez que `sql.txt` a bien été importé et que la base `Story` contient toutes les tables.
- **Sessions** : `session_start()` est appelé dans `models.php` ; ne pas le rappeler dans les vues.
- **Conditions de choix** : le format attendu est `StatNom operateur valeur` (ex : `Force>=8`) ou `nonVisited:ID` pour masquer un choix déjà visité.

---

## 📦 Évolutions possibles

- Interface d'administration pour éditer les histoires et les choix.
- Écran de fin récapitulatif avec les stats finales du personnage.
- Sauvegarde en base de données de la progression du joueur.
- Système de combat au tour par tour.

---

Ce projet est destiné à un usage éducatif — Lycée Fulbert, BTS SIO. N'hésitez pas à l'adapter pour vos propres aventures !
