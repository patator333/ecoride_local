
<div class="container mt-5">
    <h2 class="text-center mb-4">Contactez-nous</h2>

    <?php if (!empty($message)): ?>           <!-- Test si $message n'est pas vide-->
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message) ?> <!-- n'interprète pas les caractères spéciaux, et affiche le message-->
        </div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" name="nom" id="nom" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaire</label>
            <textarea name="commentaire" id="commentaire" class="form-control" rows="4" required></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success px-4">Envoyer</button>
        </div>
    </form>
</div>

