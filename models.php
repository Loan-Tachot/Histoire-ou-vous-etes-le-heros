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
// Formats supportés :
//   Force>=8        PM<8        Agi>4       PM>Force    Force==PM
//   nonVisited:26   (caché si histoire 26 déjà visitée)
function evaluerCondition($condition, $stats) {
    if (empty($condition)) return true;

    // Condition spéciale : nonVisited:ID — visible seulement si pas encore visité
    if (strpos($condition, 'nonVisited:') === 0) {
        $idHistoire = (int) substr($condition, strlen('nonVisited:'));
        $chemins    = isset($_SESSION['chemins']) ? $_SESSION['chemins'] : array();
        return !in_array($idHistoire, $chemins);
    }

    // Remplacer les noms de stats par leurs valeurs
    $expr = $condition;
    $expr = str_replace('Puissance', $stats['Puissance'], $expr);
    $expr = str_replace('Agilite',   $stats['Agilite'],   $expr);
    $expr = str_replace('Argent',    $stats['Argent'],     $expr);
    $expr = str_replace('Force',     $stats['Force'],      $expr);
    $expr = str_replace('PM',        $stats['PM'],         $expr);
    $expr = str_replace('PV',        $stats['PV'],         $expr);

    // Sécurité : n'autoriser que chiffres et opérateurs
    if (!preg_match('/^[\d\s<>=!]+$/', $expr)) return false;

    // Évaluer l'expression
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

// ── Objet ────────────────────────────────────────────────────
class Objet {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function getObtained($chemins) {
        if (!$this->pdo || empty($chemins)) return array();
        $placeholders = implode(',', array_fill(0, count($chemins), '?'));
        $s = $this->pdo->prepare(
            "SELECT o.Label, o.Effet
             FROM Objet o
             JOIN obtenue ob ON o.id = ob.id_objet
             WHERE ob.Id_histoire IN ($placeholders)"
        );
        $s->execute($chemins);
        return $s->fetchAll();
    }
}
