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

    // Vérifie que la requête vient bien d'une page interne du jeu
    // et non d'une URL tapée directement dans le navigateur
    protected function requireReferer() {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $host    = isset($_SERVER['HTTP_HOST'])    ? $_SERVER['HTTP_HOST']    : '';
        if (empty($referer) || strpos($referer, $host) === false) {
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
        $this->requireReferer();

        $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
        if ($nom === '') {
            $this->redirect('index.php?page=creer_personnage&err=1');
        }

        // Conserver les succès existants
        $succes = isset($_SESSION['succes']) ? $_SESSION['succes'] : array();

        $_SESSION['perso_nom'] = htmlspecialchars($nom);
        $_SESSION['chemins']   = array();
        $_SESSION['stats']     = statsBase();
        $_SESSION['nb_dodo']   = 0;
        $_SESSION['succes']    = $succes;

        $this->redirect('index.php?page=jeu&histoire=1');
    }
}

// ── JeuController ────────────────────────────────────────────
class JeuController extends BaseController {
    private $histoires;
    private $debouchers;
    private $objets;
    private $pdo;

    public function __construct($histoires, $debouchers, $objets, $pdo) {
        $this->histoires  = $histoires;
        $this->debouchers = $debouchers;
        $this->objets     = $objets;
        $this->pdo        = $pdo;
    }

    public function index() {
        $this->requireSession();
        $this->requireReferer();

        // Nouvelle partie demandée explicitement (bouton "Nouvelle partie")
        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            $succes = isset($_SESSION['succes']) ? $_SESSION['succes'] : array();

            $_SESSION['chemins'] = array();
            $_SESSION['stats']   = statsBase();
            $_SESSION['nb_dodo'] = 0;
            $_SESSION['succes']  = $succes;

            $this->redirect('index.php?page=jeu&histoire=1');
            return;
        }

        $idH = isset($_GET['histoire']) ? max(1, (int) $_GET['histoire']) : 1;

        // Enregistrer le chemin, recalculer les stats et détecter les nouveaux succès
        $gainStats      = array();
        $nouveauxSucces = array();

        if (!in_array($idH, $_SESSION['chemins'])) {
            $_SESSION['chemins'][] = $idH;
            $anciens               = $_SESSION['stats'];
            $_SESSION['stats']     = calculerStats($this->pdo, $_SESSION['chemins']);

            foreach ($_SESSION['stats'] as $cle => $val) {
                $delta = $val - $anciens[$cle];
                if ($delta != 0) $gainStats[$cle] = $delta;
            }

            $nouveauxSucces = $this->objets->enregistrerNouveauxSucces(array($idH));
        }

        $nom      = isset($_SESSION['perso_nom']) ? $_SESSION['perso_nom'] : '';
        $stats    = $_SESSION['stats'];
        $histoire = $this->histoires->getById($idH);

        $texte = '';
        if ($histoire) {
            $texte = htmlspecialchars($histoire['texte_histoire']);
            $texte = str_replace('Bonk', htmlspecialchars($nom), $texte);
        }

        $tousChoix = $this->debouchers->getBySource($idH);
        $choix = array_filter($tousChoix, function($c) use ($stats) {
            return evaluerCondition($c['condition_requise'], $stats);
        });
        $choix = array_values($choix);

        $this->render('jeu', array(
            'nom'            => $nom,
            'stats'          => $stats,
            'gainStats'      => $gainStats,
            'histoire'       => $histoire,
            'texte'          => $texte,
            'choix'          => $choix,
            'nouveauxSucces' => $nouveauxSucces,
        ));
    }

    public function choix() {
        $this->requireSession();
        $this->requireReferer();

        $id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $dest = $this->debouchers->getById($id);

        if ($dest) {
            $idDest = (int) $dest['Id_histoire_destination'];

            // Retour à la phase de sommeil → réinitialisation (sans toucher aux succès)
            if (in_array($idDest, array(1, 2))) {
                $_SESSION['nb_dodo'] = isset($_SESSION['nb_dodo'])
                    ? $_SESSION['nb_dodo'] + 1 : 1;

                $_SESSION['chemins'] = array();
                $_SESSION['stats']   = statsBase();
                // $_SESSION['succes'] intentionnellement conservé

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

// ── SuccesController ─────────────────────────────────────────
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

// ── MondeController ──────────────────────────────────────────
// Pas de dépendance BDD — la vue monde.php est entièrement statique
class MondeController extends BaseController {
    public function index() {
        $this->render('monde');
    }
}
