<?php if($histoire): ?>
<h2><?= htmlspecialchars($histoire['texte_histoire']) ?></h2>
<?php endif; ?>

<?php foreach($choix as $c): ?>
<a href="index.php?page=choix&id=<?= $c['Id_Deboucher'] ?>">
<button><?= htmlspecialchars($c['choix']) ?></button>
</a>
<?php endforeach; ?>

<h2>Chemins parcourus</h2>
<ul>
<?php foreach($chemins as $chemin): ?>
<li>Histoire <?= $chemin ?></li>
<?php endforeach; ?>
</ul>