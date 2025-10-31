<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    
    <div class="d-flex justify-content-end mb-3">
        <a href="index.php?page=deconnexion" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>

    <h2 class="text-center mb-4">Espace Employé</h2>

    <!-- Message flash -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Avis à valider -->
    <h4 class="mt-4">Les covoiturages demandant une validation des avis passager</h4>

    <?php if (!empty($avisNonValides)): ?>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Auteur</th>
                        <th>Trajet</th>
                        <th>Date</th>
                        <th>Commentaire</th>
                        <th>Note moyenne</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($avisNonValides as $avis): ?>
                        <tr>
                            <td><?= htmlspecialchars($avis['auteur_avis']) ?></td>
                            <td><?= htmlspecialchars($avis['lieu_depart']) ?> → <?= htmlspecialchars($avis['lieu_arrivee']) ?></td>
                            <td><?= htmlspecialchars($avis['date_avis']) ?></td>
                            <td><?= htmlspecialchars($avis['commentaire']) ?></td>
                            <td>
                                <?php
                                if ($avis['valeur1'] !== null) {
                                    $moyenne = round(
                                        ($avis['valeur1'] + $avis['valeur2'] + $avis['valeur3'] + $avis['valeur4'] + $avis['valeur5']) / 5,
                                        1
                                    );
                                    echo $moyenne . " / 5";
                                } else {
                                    echo "Non noté";
                                }
                                ?>
                            </td>
                            <td>
                                <form method="post" class="d-flex justify-content-center flex-wrap gap-1">
                                    <input type="hidden" name="id_avis" value="<?= $avis['id_avis'] ?>">
                                    <button type="submit" name="action" value="valider" class="btn btn-success btn-sm">✅ Valider</button>
                                    <button type="submit" name="action" value="refuser" class="btn btn-danger btn-sm">❌ Refuser</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center">Aucun avis en attente de validation.</p>
    <?php endif; ?>

    <!-- Covoiturages mal passés -->
    <h4 class="mt-4">Covoiturages qui se sont mal passés</h4>
    <?php if (!empty($covoituragesProbleme)): ?>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-light">
                    <tr>
                        <th>N° Covoiturage</th>
                        <th>Chauffeur</th>
                        <th>Email</th>
                        <th>Itinéraire</th>
                        <th>Date / Heure départ</th>
                        <th>Date / Heure arrivée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($covoituragesProbleme as $cov): ?>
                        <tr>
                            <td><?= htmlspecialchars($cov['id_covoiturage']) ?></td>
                            <td><?= htmlspecialchars($cov['nom_chauffeur']) ?></td>
                            <td><?= htmlspecialchars($cov['email_chauffeur']) ?></td>
                            <td><?= htmlspecialchars($cov['lieu_depart']) ?> → <?= htmlspecialchars($cov['lieu_arrivee']) ?></td>
                            <td><?= htmlspecialchars($cov['date_depart']) ?> <?= htmlspecialchars($cov['heure_depart']) ?></td>
                            <td><?= htmlspecialchars($cov['date_arrivee']) ?> <?= htmlspecialchars($cov['heure_arrivee']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center">Aucun covoiturage à problème enregistré.</p>
    <?php endif; ?>

</div>

<?php include APP_PATH . '/vues/pied_de_page.php'; ?>
