<?php
// ── BaseController ────────────────────────────────────────────
class BaseController {
    protected function render($view, $data = array()) {
        extract($data);
        include __DIR__ . "/views/$view.php";
    }
    protected function redirect($url) {
        if (strpos($url, '/') !== 0 && strpos($url, 'http') !== 0) {
            $url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/' . $url;
        }
        header("Location: $url"); exit;
    }
    protected function requireSession() {
        if (!isset($_SESSION['perso_nom'])) $this->redirect('index.php?page=accueil');
    }
    protected function requireReferer() {
        $ref  = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $host = isset($_SERVER['HTTP_HOST'])    ? $_SERVER['HTTP_HOST']    : '';
        if (empty($ref) || strpos($ref, $host) === false) $this->redirect('index.php?page=accueil');
    }
}

// ── AccueilController ─────────────────────────────────────────
class AccueilController extends BaseController {
    public function index() { $this->render('accueil'); }
}

// ── MondeController ───────────────────────────────────────────
class MondeController extends BaseController {
    public function index() { $this->render('monde'); }
}

// ── PersonnageController ──────────────────────────────────────
class PersonnageController extends BaseController {
    public function form() { $this->render('creer_personnage'); }

    public function create() {
        $this->requireReferer();
        $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
        if ($nom === '') { $this->redirect('index.php?page=creer_personnage&err=1'); }

        $succes = isset($_SESSION['succes']) ? $_SESSION['succes'] : array();
        $_SESSION['perso_nom']        = htmlspecialchars($nom);
        $_SESSION['chemins']          = array();
        $_SESSION['stats']            = statsBase();
        $_SESSION['nb_dodo']          = 0;
        $_SESSION['nb_visites_foret'] = 0;
        $_SESSION['succes']           = $succes;
        $this->redirect('index.php?page=jeu&histoire=1');
    }
}

// ── JeuController ─────────────────────────────────────────────
class JeuController extends BaseController {
    private $histoires, $debouchers, $objets, $pdo;

    public function __construct($histoires, $debouchers, $objets, $pdo) {
        $this->histoires  = $histoires;
        $this->debouchers = $debouchers;
        $this->objets     = $objets;
        $this->pdo        = $pdo;
    }

    public function index() {
        $this->requireSession();
        $this->requireReferer();

        // Nouvelle partie
        if (isset($_GET['reset'])) {
            $succes = isset($_SESSION['succes']) ? $_SESSION['succes'] : array();
            $_SESSION['chemins']          = array();
            $_SESSION['stats']            = statsBase();
            $_SESSION['nb_dodo']          = 0;
            $_SESSION['nb_visites_foret'] = 0;
            $_SESSION['succes']           = $succes;
            $this->redirect('index.php?page=jeu&histoire=1');
            return;
        }

        $idH       = isset($_GET['histoire']) ? max(1, (int)$_GET['histoire']) : 1;
        $gainStats = array(); $nouveauxSucces = array();

        if (!in_array($idH, $_SESSION['chemins'])) {
            $_SESSION['chemins'][] = $idH;

            // Incrémenter le compteur forêt à chaque nouvelle entrée (h12)
            if ($idH === 12) {
                $_SESSION['nb_visites_foret'] = isset($_SESSION['nb_visites_foret'])
                    ? $_SESSION['nb_visites_foret'] + 1 : 1;
            }

            $anciens           = $_SESSION['stats'];
            $_SESSION['stats'] = calculerStats($this->pdo, $_SESSION['chemins']);
            foreach ($_SESSION['stats'] as $cle => $val) {
                $delta = $val - $anciens[$cle];
                if ($delta != 0) $gainStats[$cle] = $delta;
            }
            $nouveauxSucces = $this->objets->enregistrerNouveauxSucces(array($idH));
        }

        $nom      = $_SESSION['perso_nom'];
        $stats    = $_SESSION['stats'];
        $histoire = $this->histoires->getById($idH);
        $texte    = '';
        if ($histoire) {
            $texte = htmlspecialchars($histoire['texte_histoire']);
            $texte = str_replace('Bonk', htmlspecialchars($nom), $texte);
        }

        $choix = array_values(array_filter(
            $this->debouchers->getBySource($idH),
            function($c) use ($stats) { return evaluerCondition($c['condition_requise'], $stats); }
        ));

        $this->render('jeu', compact('nom','stats','gainStats','histoire','texte','choix','nouveauxSucces'));
    }

    public function choix() {
        $this->requireSession();
        $this->requireReferer();

        $dest = $this->debouchers->getById(isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$dest) { $this->redirect('index.php?page=jeu&histoire=1'); return; }

        $idDest = (int)$dest['Id_histoire_destination'];

        // Retour à la phase de sommeil → réinitialisation (succès conservés)
        if (in_array($idDest, array(1, 2))) {
            $_SESSION['nb_dodo']          = (isset($_SESSION['nb_dodo']) ? $_SESSION['nb_dodo'] : 0) + 1;
            $_SESSION['chemins']          = array();
            $_SESSION['stats']            = statsBase();
            $_SESSION['nb_visites_foret'] = 0;
            if ($_SESSION['nb_dodo'] >= 20) { $this->redirect('index.php?page=jeu&histoire=3'); return; }
        }

        $this->redirect('index.php?page=jeu&histoire=' . $idDest);
    }
}

// ── SuccesController ──────────────────────────────────────────
class SuccesController extends BaseController {
    private $objets;
    public function __construct($objets) { $this->objets = $objets; }
    public function index() {
        $this->requireSession();
        $this->requireReferer();
        $this->render('succes', array(
            'succes' => $this->objets->getSucces(),
            'total'  => $this->objets->getTotalSucces(),
        ));
    }
}
