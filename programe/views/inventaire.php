<h2>Inventaire</h2>

<?php foreach($objets as $obj): ?>
<p><?= htmlspecialchars($obj['Label']) ?> : <?= htmlspecialchars($obj['Effet']) ?></p>
<?php endforeach; ?>