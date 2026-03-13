<?php
include __DIR__ . "/connexion.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM Deboucher WHERE Id_Deboucher=?");
$stmt->execute([$id]);

$choix = $stmt->fetch();
if($choix){
    header("Location: jeu.php?histoire=" . $choix['Id_histoire']);
    exit;
}
header("Location: jeu.php");
exit;
?>
