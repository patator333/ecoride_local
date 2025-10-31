<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <?php if(!empty($message_avis)): ?>
                <div class="alert alert-info text-center"><?= htmlspecialchars($message_avis) ?></div>
            <?php endif; ?>

            <div class="card p-4">
                <h3 class="card-title text-center mb-3">Donner un avis pour ce covoiturage</h3>

                <form method="POST">
                    <div class="mb-3">
                        <label for="note" class="form-label">Note :</label>
                        <select id="note" name="note" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="1">1 - Mauvais</option>
                            <option value="2">2 - Moyen</option>
                            <option value="3">3 - Correct</option>
                            <option value="4">4 - Bien</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Commentaire :</label>
                        <textarea id="commentaire" name="commentaire" class="form-control" rows="4" placeholder="Écrivez votre avis ici..." required></textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="bien_passe" name="bien_passe" value="1">
                        <label class="form-check-label" for="bien_passe">Le covoiturage s'est bien passé</label>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include APP_PATH . '/vues/pied_de_page.php'; ?>
