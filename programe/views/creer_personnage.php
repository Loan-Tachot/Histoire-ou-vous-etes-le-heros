<h2>Choisir une classe</h2>

<form method="post" action="index.php?page=creer_personnage">

<select name="classe">

<?php foreach($classes as $c): ?>
<option value="<?= $c['id'] ?>"><?= $c['Nom_classe'] ?></option>
<?php endforeach; ?>

</select>

<button>Créer</button>

</form>