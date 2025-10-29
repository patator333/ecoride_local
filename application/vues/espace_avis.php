<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-4">
    <h3 class="text-center mb-4">Donner un avis pour ce covoiturage</h3>

    <?php if(!empty($message_avis)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message_avis) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3 text-center">
            <label for="note" class="form-label">Note :</label>
            <select name="note" id="note" class="form-select w-auto d-inline-block">
                <option value="">-- Choisir --</option>
                <?php for($i=1; $i<=5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Commentaire :</label>
            <textarea name="message" id="message" class="form-control" rows="4" placeholder="Écrivez votre avis ici..."></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
            <a href="index.php?page=espace_utilisateur" class="btn btn-secondary">Retour</a>
        </div>
    </form>

    <?php if(!empty($avis_list)): ?>
        <h4 class="mt-4">Avis existants</h4>
        <ul class="list-group">
            <?php foreach($avis_list as $avis): ?>
                <li class="list-group-item">
                    <strong>Note :</strong> <?= (int)$avis['note'] ?> / 5<br>
                    <strong>Commentaire :</strong> <?= htmlspecialchars($avis['message']) ?><br>
                    <small class="text-muted">Par <?= htmlspecialchars($avis['nom_utilisateur']) ?> le <?= htmlspecialchars($avis['date_ajout']) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
