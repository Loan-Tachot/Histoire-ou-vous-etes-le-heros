<?php
// ── Connexion ────────────────────────────────────────────────
if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

$host = isset($host) ? $host : '192.168.56.200';
$db   = isset($db)   ? $db   : 'Story';
$user = isset($user) ? $user : 'operateur';
$pass = isset($pass) ? $pass : 'Bonk';

$pdo      = null;
$db_error = null;

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user, $pass,
        array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        )
    );
} catch (PDOException $e) {
    $pdo      = null;
    $db_error = "Connexion BDD impossible : " . $e->getMessage();
}

$GLOBALS['db_error'] = $db_error;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Stats de base ────────────────────────────────────────────
function statsBase() {
    return array(
        'PV'        => 100,
        'Force'     => 10,
        'Agilite'   => 10,
        'PM'        => 5,
        'Puissance' => 10,
        'Argent'    => 100,
    );
}

// ── Recalcule les stats depuis les chemins parcourus ─────────
function calculerStats($pdo, $chemins) {
    $stats = statsBase();
    if (!$pdo || empty($chemins)) return $stats;

    $placeholders = implode(',', array_fill(0, count($chemins), '?'));
    $s = $pdo->prepare(
        "SELECT a.bonus_PV, a.bonus_Force, a.bonus_Agi, a.Bonus_PM, a.bonus_Puissance, a.Bonus_Argent
         FROM histoire h
         JOIN Amelioration a ON h.Id_Amelioration = a.Id_Amelioration
         WHERE h.Id_histoire IN ($placeholders)"
    );
    $s->execute($chemins);
    foreach ($s->fetchAll() as $row) {
        $stats['PV']        += $row['bonus_PV'];
        $stats['Force']     += $row['bonus_Force'];
        $stats['Agilite']   += $row['bonus_Agi'];
        $stats['PM']        += $row['Bonus_PM'];
        $stats['Puissance'] += $row['bonus_Puissance'];
        $stats['Argent']    += $row['Bonus_Argent'];
    }
    return $stats;
}

// ── Évalue une condition de stat ─────────────────────────────
function evaluerCondition($condition, $stats) {
    if (empty($condition)) return true;

    if (strpos($condition, 'nonVisited:') === 0) {
        $idHistoire = (int) substr($condition, strlen('nonVisited:'));
        $chemins    = isset($_SESSION['chemins']) ? $_SESSION['chemins'] : array();
        return !in_array($idHistoire, $chemins);
    }

    $expr = $condition;
    $expr = str_replace('Puissance', $stats['Puissance'], $expr);
    $expr = str_replace('Agilite',   $stats['Agilite'],   $expr);
    $expr = str_replace('Argent',    $stats['Argent'],     $expr);
    $expr = str_replace('Force',     $stats['Force'],      $expr);
    $expr = str_replace('PM',        $stats['PM'],         $expr);
    $expr = str_replace('PV',        $stats['PV'],         $expr);

    if (!preg_match('/^[\d\s<>=!]+$/', $expr)) return false;

    return eval("return ($expr);");
}

// ── Histoire ─────────────────────────────────────────────────
class Histoire {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getById($id) {
        if (!$this->pdo) return null;
        $s = $this->pdo->prepare("SELECT * FROM histoire WHERE Id_histoire = ?");
        $s->execute(array($id));
        $row = $s->fetch();
        return $row ? $row : null;
    }
}

// ── Deboucher ────────────────────────────────────────────────
class Deboucher {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getById($id) {
        if (!$this->pdo) return null;
        $s = $this->pdo->prepare("SELECT * FROM Deboucher WHERE Id_Deboucher = ?");
        $s->execute(array($id));
        $row = $s->fetch();
        return $row ? $row : null;
    }

    public function getBySource($sourceId) {
        if (!$this->pdo) return array();
        $s = $this->pdo->prepare(
            "SELECT * FROM Deboucher WHERE Id_histoire_source = ? ORDER BY Id_Deboucher ASC"
        );
        $s->execute(array($sourceId));
        return $s->fetchAll();
    }
}

// ── Objet / Succès ───────────────────────────────────────────
// Les objets de la BDD sont utilisés comme succès.
// Ils sont stockés dans $_SESSION['succes'] sous la forme :
//   [ id_objet => timestamp_deblocage, ... ]
// Cette clé de session n'est JAMAIS réinitialisée entre les parties.
class Objet {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // Retourne tous les succès débloqués avec leur date
    public function getSucces() {
        if (!$this->pdo) return array();
        $ids = isset($_SESSION['succes']) ? array_keys($_SESSION['succes']) : array();
        if (empty($ids)) return array();

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $s = $this->pdo->prepare(
            "SELECT id, Label, Effet FROM Objet WHERE id IN ($placeholders)"
        );
        $s->execute($ids);
        $objets = $s->fetchAll();

        foreach ($objets as &$o) {
            $o['debloque_le'] = isset($_SESSION['succes'][$o['id']])
                ? $_SESSION['succes'][$o['id']] : '';
        }
        return $objets;
    }

    // Retourne le total des succès disponibles dans la BDD
    public function getTotalSucces() {
        if (!$this->pdo) return 0;
        return (int) $this->pdo->query("SELECT COUNT(*) FROM Objet")->fetchColumn();
    }

    // Détecte et enregistre les nouveaux succès débloqués par les chemins donnés
    // Retourne uniquement ceux qui viennent d'être débloqués (pour la notification)
    public function enregistrerNouveauxSucces($chemins) {
        if (!$this->pdo || empty($chemins)) return array();

        $placeholders = implode(',', array_fill(0, count($chemins), '?'));
        $s = $this->pdo->prepare(
            "SELECT o.id, o.Label, o.Effet
             FROM Objet o
             JOIN obtenue ob ON o.id = ob.id_objet
             WHERE ob.Id_histoire IN ($placeholders)"
        );
        $s->execute($chemins);
        $tous = $s->fetchAll();

        if (!isset($_SESSION['succes'])) {
            $_SESSION['succes'] = array();
        }

        $nouveaux = array();
        foreach ($tous as $o) {
            if (!array_key_exists($o['id'], $_SESSION['succes'])) {
                $_SESSION['succes'][$o['id']] = date('d/m/Y');
                $nouveaux[] = $o;
            }
        }
        return $nouveaux;
    }
}
