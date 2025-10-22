<?php include APP_PATH . '/vues/entete.php';?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Connexion</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary px-4">Valider</button>
        </div>
    </form>
</div>

<?php include APP_PATH . '/vues/pied_de_page.php';?>
 