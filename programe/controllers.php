<?php
// controllers.php - All controller classes
// require_once __DIR__ . '/models.php'; // Moved to individual controllers

class BaseController {
    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/views/' . $view . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}

class AccueilController extends BaseController {
    public function index() {
        $this->render('accueil', []);
    }
}

class IndexController extends BaseController {
    public function index() {
        require_once __DIR__ . '/models.php';
        // Check if accessed directly, redirect to accueil
        if(basename($_SERVER['SCRIPT_NAME']) === 'index.php'){
            $this->redirect('../accueil.php');
        }

        $personnage = null;
        $stats = [];
        if(isset($_SESSION['personnage'])){
            $personnageModel = new Personnage();
            $personnage = $personnageModel->getById($_SESSION['personnage']['Id']);
            $stats = $personnage ? $personnage->getStats() : [];
        }

        $base = dirname($_SERVER['SCRIPT_NAME']) . '/';
        $this->render('index', ['personnage' => $personnage, 'base' => $base, 'stats' => $stats]);
    }
}

class CreerPersonnageController extends BaseController {
    public function index() {
        require_once __DIR__ . '/models.php';
        $classeModel = new Classe();
        $classes = $classeModel->getAll();

        $this->render('creer_personnage', ['classes' => $classes]);
    }

    public function create() {
        require_once __DIR__ . '/models.php';
        if(isset($_POST['classe'])){
            $classe = (int) $_POST['classe'];

            $personnageModel = new Personnage();
            $id = $personnageModel->create([
                'pv' => 100,
                'force' => 10,
                'agi' => 10,
                'pm' => 5,
                'puissance' => 10,
                'argent' => 100,
                'id_classe' => $classe
            ]);

            $_SESSION['personnage'] = $id;

            $this->redirect('index.php?page=jeu');
        }
    }
}

class JeuController extends BaseController {
    public function index() {
        require_once __DIR__ . '/models.php';
        $id_histoire = 1;
        if(isset($_GET['histoire'])){
            $id_histoire = (int) $_GET['histoire'];
        }

        // Add to chemins parcourus
        if(!isset($_SESSION['chemins'])){
            $_SESSION['chemins'] = [];
        }
        if(!in_array($id_histoire, $_SESSION['chemins'])){
            $_SESSION['chemins'][] = $id_histoire;
        }

        $histoireModel = new Histoire();
        $histoire = $histoireModel->getById($id_histoire);

        $deboucherModel = new Deboucher();
        $choix = $deboucherModel->getByHistoireId($id_histoire);

        $this->render('jeu', ['histoire' => $histoire, 'choix' => $choix, 'chemins' => $_SESSION['chemins']]);
    }
}

class ChoixController extends BaseController {
    public function index() {
        require_once __DIR__ . '/models.php';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $deboucherModel = new Deboucher();
        $choix = $deboucherModel->getById($id);

        if($choix){
            $this->redirect('index.php?page=jeu&histoire=' . $choix['Id_histoire_1']);
        } else {
            $this->redirect('index.php?page=jeu');
        }
    }
}

class InventaireController extends BaseController {
    public function index() {
        require_once __DIR__ . '/models.php';
        $objetModel = new Objet();
        $objets = $objetModel->getObtained();

        $this->render('inventaire', ['objets' => $objets]);
    }
}
?>