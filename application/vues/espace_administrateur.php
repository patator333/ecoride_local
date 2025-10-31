<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">

    <!-- Bouton Déconnexion -->
    <div class="d-flex justify-content-end mb-3">
        <a href="index.php?page=deconnexion" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>

    <h2 class="text-center mb-4">Espace Administrateur</h2>

    <!-- Création des comptes employés -->
    <h4>Création des comptes employés</h4>
    <?php if(!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4 mx-auto" style="max-width: 500px;">
        <input type="hidden" name="creer_employe" value="1">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" name="nom" id="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="mail" class="form-label">Email</label>
            <input type="email" name="mail" id="mail" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary w-100">Valider la création de compte</button>
        </div>
    </form>

    <!-- Graphiques -->
    <h4 class="mt-5">Statistiques des 7 derniers jours</h4>
    <div class="row">
        <div class="col-lg-6 col-12 mb-4">
            <div class="p-3 border rounded" style="height:300px;">
                <canvas id="graphCovoiturages"></canvas>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-4">
            <div class="p-3 border rounded" style="height:300px;">
                <canvas id="graphCredits"></canvas>
            </div>
        </div>
    </div>

    <!-- Liste des comptes -->
    <h4 class="mt-4">Liste des comptes</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date création</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead> 
            <tbody>
                <?php foreach($comptes as $compte): ?>
                    <tr>
                        <td><?= htmlspecialchars($compte['nom']) ?></td>
                        <td><?= htmlspecialchars($compte['mail']) ?></td>
                        <td><?= htmlspecialchars($compte['date_creation']) ?></td>
                        <td>
                            <?= $compte['id_type_compte'] == 1 ? 'Utilisateur' : ($compte['id_type_compte'] == 2 ? 'Employé' : 'Administrateur') ?>
                        </td>
                        <td><?= $compte['actif'] ? 'Actif' : 'Suspendu' ?></td>
                        <td>
                            <?php if($compte['actif']): ?>
                                <a href="<?= PUBLIC_URL ?>/?page=espace_administrateur&action=suspendre&id=<?= $compte['id_utilisateur'] ?>" class="btn btn-danger btn-sm mb-1">Suspendre</a>
                            <?php else: ?>
                                <a href="<?= PUBLIC_URL ?>/?page=espace_administrateur&action=activer&id=<?= $compte['id_utilisateur'] ?>" class="btn btn-success btn-sm mb-1">Activer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js -->
<script src="<?= PUBLIC_URL ?>/js/chart.min.js"></script>
<script>
    Chart.defaults.maintainAspectRatio = false;

    const covoData = {
        labels: <?= json_encode(array_column($covoiturages_semaine, 'jour')) ?>,
        datasets: [{
            label: 'Nombre de covoiturages',
            data: <?= json_encode(array_column($covoiturages_semaine, 'nb_covoiturages')) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
    };

    const creditsData = {
        labels: <?= json_encode(array_column($credits_semaine, 'jour')) ?>,
        datasets: [{
            label: 'Crédits gagnés',
            data: <?= json_encode(array_column($credits_semaine, 'total_credits')) ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.6)'
        }]
    };

    new Chart(document.getElementById('graphCovoiturages'), { type: 'bar', data: covoData, options: { responsive:true } });
    new Chart(document.getElementById('graphCredits'), { type: 'bar', data: creditsData, options: { responsive:true } });
</script>

<?php include APP_PATH . '/vues/pied_de_page.php'; ?>
