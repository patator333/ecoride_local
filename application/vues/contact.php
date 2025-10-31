<div class="container mt-5">
    <h2 class="text-center mb-4">Contactez-nous</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mx-auto p-4 border rounded shadow-sm" style="max-width: 500px; background-color: #f8f9fa;">
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

        <div class="d-grid">
            <button type="submit" class="btn btn-success btn-block">Envoyer</button>
        </div>
    </form>
</div>
