<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/models.php';
require_once __DIR__ . '/controllers.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

switch ($page) {
    case 'creer_personnage':
        (new PersonnageController())->form();
        break;
    case 'save_personnage':
        (new PersonnageController())->create();
        break;
    case 'jeu':
        (new JeuController(new Histoire($pdo), new Deboucher($pdo), $pdo))->index();
        break;
    case 'choix':
        (new JeuController(new Histoire($pdo), new Deboucher($pdo), $pdo))->choix();
        break;
    case 'inventaire':
        (new InventaireController(new Objet($pdo)))->index();
        break;
    default:
        (new AccueilController())->index();
        break;
}
