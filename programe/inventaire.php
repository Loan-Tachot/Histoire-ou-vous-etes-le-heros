<?php
include __DIR__ . "/connexion.php";

$sql = "
SELECT Objet.Label, Objet.Effet
FROM Objet
JOIN obtenue ON Objet.id = obtenue.id
";

$res = $pdo->query($sql);

echo "<h2>Inventaire</h2>";

foreach($res as $obj){
    echo "<p>" . htmlspecialchars($obj['Label']) . " : " . htmlspecialchars($obj['Effet']) . "</p>";
}
?>
