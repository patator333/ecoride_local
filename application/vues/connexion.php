<?php include APP_PATH . '/vues/entete.php';?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Connexion</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mx-auto p-4 border rounded shadow-sm" style="max-width: 400px; background-color: #f8f9fa;">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-block">Valider</button>
        </div>
    </form>
</div>

<?php include APP_PATH . '/vues/pied_de_page.php';?>
