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
        $_SESSION['perso_nom']    = htmlspecialchars($nom);
        $_SESSION['chemins']      = array();
        $_SESSION['stats']        = statsBase();
        $_SESSION['nb_dodo']      = 0;   // compteur sommeil
        $this->redirect('index.php?page=jeu&histoire=1');
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
        $gainStats = array();
        if (!in_array($idH, $_SESSION['chemins'])) {
            $_SESSION['chemins'][] = $idH;
            $anciens               = $_SESSION['stats'];
            $_SESSION['stats']     = calculerStats($this->pdo, $_SESSION['chemins']);
            // Calculer les gains pour affichage
            foreach ($_SESSION['stats'] as $cle => $val) {
                $delta = $val - $anciens[$cle];
                if ($delta != 0) $gainStats[$cle] = $delta;
            }
        }

        $nom      = isset($_SESSION['perso_nom']) ? $_SESSION['perso_nom'] : '';
        $stats    = $_SESSION['stats'];
        $histoire = $this->histoires->getById($idH);

        // Remplacer Bonk par le nom du joueur
        $texte = '';
        if ($histoire) {
            $texte = htmlspecialchars($histoire['texte_histoire']);
            $texte = str_replace('Bonk', htmlspecialchars($nom), $texte);
        }

        // Récupérer tous les choix puis filtrer selon les conditions
        $tousChoix = $this->debouchers->getBySource($idH);
        $choix = array_filter($tousChoix, function($c) use ($stats) {
            return evaluerCondition($c['condition_requise'], $stats);
        });
        $choix = array_values($choix);

        $this->render('jeu', array(
            'nom'      => $nom,
            'stats'    => $stats,
            'gainStats'=> $gainStats,
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
            $idDest = (int) $dest['Id_histoire_destination'];

            // Compteur sommeil : histoires 1 et 2 = dormir encore
            // Histoire 2 = "Dormir encore" — on incrémente
            if (in_array($idDest, array(1, 2))) {
                $_SESSION['nb_dodo'] = isset($_SESSION['nb_dodo'])
                    ? $_SESSION['nb_dodo'] + 1 : 1;
                // 20ème fois → mort
                if ($_SESSION['nb_dodo'] >= 20) {
                    $this->redirect('index.php?page=jeu&histoire=3');
                    return;
                }
            }

            $this->redirect('index.php?page=jeu&histoire=' . $idDest);
        } else {
            $this->redirect('index.php?page=jeu&histoire=1');
        }
    }
}

// ── InventaireController ─────────────────────────────────────
class InventaireController extends BaseController {
    private $objets;
    public function __construct($objets) { $this->objets = $objets; }

    public function index() {
        $this->requireSession();
        $chemins = isset($_SESSION['chemins']) ? $_SESSION['chemins'] : array();
        $this->render('inventaire', array(
            'objets' => $this->objets->getObtained($chemins),
        ));
    }
}
