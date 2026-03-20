<?php
$host = '192.168.56.200';
$db   = 'Story';
$user = 'operateur';
$pass = 'Bonk';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "✔ Connexion réussie !<br>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables : " . implode(', ', $tables);
} catch (PDOException $e) {
    echo "✘ Erreur : " . $e->getMessage();
}
