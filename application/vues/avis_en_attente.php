<?php
require_once APP_PATH . '/modeles/avis.php';

$avis = getAvisEnAttente();
?>
<h3>Avis en attente de validation</h3>
<?php if (!$avis) echo "<p>Aucun avis en attente.</p>"; ?>
<table class="table table-bordered">
    <tr>
        <th>Utilisateur</th>
        <th>Covoiturage</th>
        <th>Date</th>
        <th>Note</th>
        <th>Commentaire</th>
        <th>Actions</th>
    </tr>
<?php foreach ($avis as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a['nom_utilisateur']) ?></td>
        <td><?= htmlspecialchars($a['lieu_depart'] . ' → ' . $a['lieu_arrivee']) ?></td>
        <td><?= htmlspecialchars($a['date_depart']) ?></td>
        <td><?= $a['note'] ?></td>
        <td><?= htmlspecialchars($a['commentaire']) ?></td>
        <td>
            <a href="?page=valider_avis&id_avis=<?= $a['id_avis'] ?>&action=validé" class="btn btn-success btn-sm">Valider</a>
            <a href="?page=valider_avis&id_avis=<?= $a['id_avis'] ?>&action=rejeté" class="btn btn-danger btn-sm">Rejeter</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
