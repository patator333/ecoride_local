<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    
    <div class="d-flex justify-content-end mb-3">
        <a href="index.php?page=deconnexion" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>

    <h2 class="text-center mb-4">Espace Employé</h2>

    <!-- Avis à valider -->
    <h4>Les covoiturages demandant une validation des avis passager</h4>
    <?php if (!empty($avis_non_valide)): ?>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>N° Covoiturage</th>
                        <th>Itinéraire</th>
                        <th>Coût</th>
                        <th>Véhicule</th>
                        <th>Avis</th>
                        <th>Commentaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($avis_non_valide as $avis): ?>
                        <tr>
                            <td><?= htmlspecialchars($avis['id_covoiturage']) ?></td>
                            <td><?= htmlspecialchars($avis['lieu_depart']) ?> → <?= htmlspecialchars($avis['lieu_arrivee']) ?></td>
                            <td><?= htmlspecialchars($avis['prix_par_personne']) ?> €</td>
                            <td><?= htmlspecialchars($avis['marque']) ?> <?= htmlspecialchars($avis['modele']) ?></td>
                            <td><?= ($avis['note_moyenne'] ?? 0) >= 3 ? 'Positif' : 'Négatif' ?></td>
                            <td><?= htmlspecialchars($avis['commentaire']) ?></td>
                            <td>
                                <a href="<?= PUBLIC_URL ?>/?page=valider_avis&id=<?= $avis['id_avis'] ?>&action=publier" class="btn btn-success btn-sm">Publier</a>
                                <a href="<?= PUBLIC_URL ?>/?page=valider_avis&id=<?= $avis['id_avis'] ?>&action=refuser" class="btn btn-danger btn-sm">Ne pas publier</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Aucun avis en attente de validation.</p>
    <?php endif; ?>

    <!-- Covoiturages mal passés -->
    <h4>Covoiturages qui se sont mal passés</h4>
    <?php if (!empty($covoiturages_probleme)): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
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
                    <?php foreach($covoiturages_probleme as $cov): ?>
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
        <p>Aucun covoiturage à problème enregistré.</p>
    <?php endif; ?>
</div>

<?php include APP_PATH . '/vues/pied_de_page.php'; ?>
 