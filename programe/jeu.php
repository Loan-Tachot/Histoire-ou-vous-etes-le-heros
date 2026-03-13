<?php
include __DIR__ . "/connexion.php";

$id_histoire = 1;
if(isset($_GET['histoire'])){
    $id_histoire = (int) $_GET['histoire'];
}

$stmt = $pdo->prepare("SELECT * FROM histoire WHERE Id_histoire=?");
$stmt->execute([$id_histoire]);
$histoire = $stmt->fetch();
if($histoire){
    echo "<h2>" . htmlspecialchars($histoire['texte_histoire']) . "</h2>";
}

$stmt2 = $pdo->prepare("SELECT * FROM Deboucher WHERE Id_histoire=?");
$stmt2->execute([$id_histoire]);

foreach($stmt2 as $choix){
    echo "<a href='choix.php?id=" . $choix['Id_Deboucher'] . "'>
    <button>" . htmlspecialchars($choix['choix']) . "</button>
    </a>";
}
?>