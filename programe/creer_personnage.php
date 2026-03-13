<?php
include __DIR__ . "/connexion.php";

$classes = $pdo->query("SELECT * FROM Classe");

if(isset($_POST['classe'])){
    $classe = (int) $_POST['classe'];

    $sql = "INSERT INTO personnage 
    (PV_base,Force_base,Agilite_base,PM_base,Puissance_base,argent_base,id_1)
    VALUES (100,10,10,5,10,100,?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$classe]);

    $_SESSION['personnage'] = $pdo->lastInsertId();

    header("Location: jeu.php");
    exit;
}
?>


<h2>Choisir une classe</h2>

<form method="post">

<select name="classe">

<?php
foreach($classes as $c){
echo "<option value='".$c['id']."'>".$c['Nom_classe']."</option>";
}
?>

</select>

<button>Créer</button>

</form>