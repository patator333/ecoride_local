<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Avis pour le covoiturage</h2>

    <?php if ($message_avis): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message_avis) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="note" class="form-label">Note (1 à 5)</label>
            <input type="number" name="note" id="note" class="form-control" min="1" max="5" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Commentaire</label>
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
        <a href="index.php?page=espace_utilisateur" class="btn btn-secondary">Retour</a>
    </form>

    <hr>

    <h4>Avis existants</h4>
    <?php if (!empty($avis_list)): ?>
        <ul class="list-group">
            <?php foreach ($avis_list as $a): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($a['nom_utilisateur']) ?></strong> 
                    (<?= date('d/m/Y H:i', strtotime($a['date_avis'])) ?>) :
                    <span>Note: <?= $a['note'] ?>/5</span>
                    <p><?= nl2br(htmlspecialchars($a['message'])) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun avis pour le moment.</p>
    <?php endif; ?>
</div>
