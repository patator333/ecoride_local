<?php include APP_PATH . '/vues/entete.php';?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Recherche de covoiturage</h2>

    <!-- Formulaire de recherche -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="page" value="recherche_covoiturage">
        <div class="col-md-4">
            <input type="text" name="ville_depart" class="form-control" placeholder="Ville de départ" 
                   value="<?= htmlspecialchars($ville_depart ?? '') ?>" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="ville_arrivee" class="form-control" placeholder="Ville d'arrivée" 
                   value="<?= htmlspecialchars($ville_arrivee ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="date_depart" class="form-control" 
                   value="<?= htmlspecialchars($date_depart ?? '') ?>" required>
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Valider</button>
        </div>
    </form>

    <!-- Résultats -->
    <?php if (!empty($covoiturages)): ?>
        <div class="row">
            <?php foreach($covoiturages as $cov): ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($cov['lieu_depart'] . " → " . $cov['lieu_arrivee']) ?></h5>
                            <p class="mb-1">Date : <?= htmlspecialchars($cov['date_depart']) ?></p>
                            <p class="mb-1">Heure : <?= htmlspecialchars($cov['heure_depart']) ?></p>
                            <p class="mb-1">Crédit demandé : <?= htmlspecialchars($cov['prix_par_personne']) ?> €</p>
                            <p class="mb-1">Places restantes : <?= htmlspecialchars($cov['nombre_places']) ?></p>
                            <p class="mb-1">Chauffeur : <?= htmlspecialchars($cov['nom_chauffeur'] ?? '') ?></p>
                            <p class="mb-1">
                                <?php if(!empty($cov['photo_chauffeur'])): ?>
                                    <img src="<?= PUBLIC_URL ?>/uploads/<?= htmlspecialchars($cov['photo_chauffeur']) ?>" 
                                         alt="Photo chauffeur" class="img-thumbnail" style="max-width:80px;">
                                <?php else: ?>
                                    <img src="<?= PUBLIC_URL ?>/images/man.png" 
                                         alt="Logo chauffeur" class="img-thumbnail" style="max-width:80px;">
                                <?php endif; ?>
                            </p>
                            <p class="mb-1">Note : <?= htmlspecialchars($cov['note_moyenne'] ?? '0') ?> ⭐</p>
                            <?php if(!empty($cov['electrique'])): ?>
                                <p class="mb-1"><img src="<?= PUBLIC_URL ?>/assets/logo_electrique.png" alt="Électrique" style="width:24px;"></p>
                            <?php endif; ?>

                            <!-- Bouton voir détails -->
                            <button class="btn btn-info btn-sm mt-2" type="button" onclick="toggleDetails('details-<?= $cov['id_covoiturage'] ?>')">
                                Voir détails
                            </button>

                            <!-- Section détails cachée -->
                            <div id="details-<?= $cov['id_covoiturage'] ?>" class="mt-2" style="display:none; border-top:1px solid #ddd; padding-top:10px;">
                                <p>Marque véhicule : <?= htmlspecialchars($cov['marque']) ?></p>
                                <p>Modèle véhicule : <?= htmlspecialchars($cov['modele']) ?></p>
                                <?php if (!empty($cov['preferences'])): ?>
                                    <p>Préférences chauffeur :</p>
                                    <ul>
                                        <li>Fumeur : <?= $cov['preferences']['fumeur'] ? 'Oui' : 'Non' ?></li>
                                        <li>Animaux : <?= $cov['preferences']['animal'] ? 'Oui' : 'Non' ?></li>
                                        <li>Remarques : <?= htmlspecialchars($cov['preferences']['remarques_particulieres'] ?? '-') ?></li>
                                    </ul>
                                <?php else: ?>
                                    <p>Aucune préférence renseignée.</p>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2 text-end">
                                <a href="<?= PUBLIC_URL ?>/?page=participer&id=<?= $cov['id_covoiturage'] ?>" class="btn btn-success">Participer</a>
                            </div>
                        </div>
                    </div>  
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-3">
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page_num) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= PUBLIC_URL ?>/?page=recherche_covoiturage&ville_depart=<?= urlencode($ville_depart) ?>&ville_arrivee=<?= urlencode($ville_arrivee) ?>&date_depart=<?= $date_depart ?>&page_num=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php else: ?>
        <p class="text-center">Aucun covoiturage trouvé.</p>
    <?php endif; ?>
</div>

<script src="<?= PUBLIC_URL ?>/js/script.js"></script>

<?php include APP_PATH . '/vues/pied_de_page.php';?>