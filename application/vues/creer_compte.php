<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Créer un compte</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto p-4 border rounded shadow-sm" style="max-width: 450px; background-color: #f8f9fa;">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="mail" class="form-label">Email</label>
            <input type="email" id="mail" name="mail" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success btn-block">
                Valider la création du compte et l'attribution de 20 crédits
            </button>
        </div>
    </form>
</div>

<?php include APP_PATH . '/vues/pied_de_page.php'; ?>
