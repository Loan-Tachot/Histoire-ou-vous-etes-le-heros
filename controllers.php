<?php
// ── BaseController ───────────────────────────────────────────
class BaseController {
    protected function render($view, $data = array()) {
        extract($data);
        include __DIR__ . "/views/$view.php";
    }

    protected function redirect($url) {
        if (strpos($url, '/') !== 0 && strpos($url, 'http') !== 0) {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $url  = $base . '/' . $url;
        }
        header("Location: $url");
        exit;
    }

    protected function requireSession() {
        if (!isset($_SESSION['perso_nom'])) {
            $this->redirect('index.php?page=accueil');
        }
    }
}

// ── AccueilController ────────────────────────────────────────
class AccueilController extends BaseController {
    public function index() {
        $this->render('accueil');
    }
}

// ── PersonnageController ─────────────────────────────────────
class PersonnageController extends BaseController {
    public function form() {
        $this->render('creer_personnage');
    }

    public function create() {
        $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
        if ($nom === '') {
            $this->redirect('index.php?page=creer_personnage&err=1');
        }

        // Initialisation de la session — aucune BDD nécessaire
        $_SESSION['perso_nom'] = htmlspecialchars($nom);
        $_SESSION['chemins']   = array();
        $_SESSION['stats']     = statsBase();

        $this->redirect('index.php?page=jeu');
    }
}

// ── JeuController ────────────────────────────────────────────
class JeuController extends BaseController {
    private $histoires;
    private $debouchers;
    private $pdo;

    public function __construct($histoires, $debouchers, $pdo) {
        $this->histoires  = $histoires;
        $this->debouchers = $debouchers;
        $this->pdo        = $pdo;
    }

    public function index() {
        $this->requireSession();

        $idH = isset($_GET['histoire']) ? max(1, (int) $_GET['histoire']) : 1;

        // Enregistrer le chemin et recalculer les stats
        if (!in_array($idH, $_SESSION['chemins'])) {
            $_SESSION['chemins'][] = $idH;
            $_SESSION['stats']     = calculerStats($this->pdo, $_SESSION['chemins']);
        }

        $histoire = $this->histoires->getById($idH);
        $choix    = $this->debouchers->getBySource($idH);

        $nom = isset($_SESSION['perso_nom']) ? $_SESSION['perso_nom'] : '';

        // Remplacer 'Bonk' par le nom du joueur dans le texte
        $texte = '';
        if ($histoire) {
            $texte = htmlspecialchars($histoire['texte_histoire']);
            $texte = str_replace('Bonk', htmlspecialchars($nom), $texte);
        }

        $this->render('jeu', array(
            'nom'     => $_SESSION['perso_nom'],
            'stats'   => $_SESSION['stats'],
            'histoire'=> $histoire,
            'texte'   => $texte,
            'choix'   => $choix,
        ));
    }

    public function choix() {
        $this->requireSession();
        $id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $dest = $this->debouchers->getById($id);
        if ($dest) {
            $this->redirect('index.php?page=jeu&histoire=' . $dest['Id_histoire_destination']);
        } else {
            $this->redirect('index.php?page=jeu');
        }
    }
}

// ── InventaireController ─────────────────────────────────────
class InventaireController extends BaseController {
    private $objets;

    public function __construct($objets) {
        $this->objets = $objets;
    }

    public function index() {
        $this->requireSession();
        $chemins = isset($_SESSION['chemins']) ? $_SESSION['chemins'] : array();
        $this->render('inventaire', array(
            'objets' => $this->objets->getObtained($chemins),
        ));
    }
}
