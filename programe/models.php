<?php
// models.php - All model classes and database connection

// possibilité de charger un fichier de configuration local sans toucher
// au code existant. Créez `config.local.php` à la racine du projet
// avec vos propres valeurs (ce fichier ne doit pas être commité).
if(file_exists(__DIR__ . '/../config.local.php')){
    include __DIR__ . '/../config.local.php';
}

// paramètres par défaut
$host     = $host     ?? 'histoire-heros.com';
$db       = $db       ?? 'jeu';
$user     = $user     ?? 'root';
$password = $password ?? '';

// affichage des erreurs uniquement en local (domaine contenant "localhost")
if(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

try{
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    die("Erreur : " . $e->getMessage());
}

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

class Character {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getId() {
        return $this->data['Id'];
    }

    public function getStats() {
        return [
            'PV' => $this->data['PV_base'],
            'Force' => $this->data['Force_base'],
            'Agilité' => $this->data['Agilite_base'],
            'PM' => $this->data['PM_base'],
            'Puissance' => $this->data['Puissance_base'],
            'Argent' => $this->data['argent_base']
        ];
    }

    public function getClasseId() {
        return $this->data['id_1'];
    }
}

class Classe {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM Classe")->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Personnage {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM personnage WHERE Id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Character($data) : null;
    }

    public function getStats($id) {
        $character = $this->getById($id);
        return $character ? $character->getStats() : [];
    }
}

class Histoire {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM histoire WHERE Id_histoire = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

class Deboucher {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Deboucher WHERE Id_Deboucher = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByHistoireId($id_histoire) {
        $stmt = $this->pdo->prepare("SELECT * FROM Deboucher WHERE Id_histoire = ?");
        $stmt->execute([$id_histoire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Objet {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getObtained() {
        $sql = "
        SELECT Objet.Label, Objet.Effet
        FROM Objet
        JOIN Asso_8 ON Objet.id = Asso_8.id
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>