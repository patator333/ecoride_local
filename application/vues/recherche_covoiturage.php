<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Recherche de covoiturage</h2>

    <!-- Formulaire de recherche -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="page" value="recherche_covoiturage">
        <div class="col-md-4">
            <input type="text" name="ville_depart" class="form-control" placeholder="Ville de départ" 
                   value="<?= htmlspecialchars((string)($ville_depart ?? '')) ?>" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="ville_arrivee" class="form-control" placeholder="Ville d'arrivée" 
                   value="<?= htmlspecialchars((string)($ville_arrivee ?? '')) ?>" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="date_depart" class="form-control" 
                   value="<?= htmlspecialchars((string)($date_depart ?? '')) ?>" required>
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Valider</button>
        </div>
    </form> 

    <!-- Résultats -->
    <?php if (!empty($covoiturages) && is_array($covoiturages)): ?>
        <div class="row">
            <?php foreach($covoiturages as $cov): ?>
                <?php $cov_id = $cov['id_covoiturage'] ?? 0; ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= 
                                htmlspecialchars((string)($cov['lieu_depart'] ?? '-') . " → " . ($cov['lieu_arrivee'] ?? '-')) 
                            ?></h5>
                            <p class="mb-1">Date : <?= htmlspecialchars((string)($cov['date_depart'] ?? '-')) ?></p>
                            <p class="mb-1">Heure : <?= htmlspecialchars((string)($cov['heure_depart'] ?? '-')) ?></p>
                            <p class="mb-1">Crédit demandé : <?= htmlspecialchars((string)($cov['prix_par_personne'] ?? '-')) ?> €</p>
                            <p class="mb-1">Places restantes : <?= htmlspecialchars((string)($cov['nombre_places'] ?? '-')) ?></p>
                            <p class="mb-1">Chauffeur : <?= htmlspecialchars((string)($cov['nom_chauffeur'] ?? '-')) ?></p>
                            <p class="mb-1">
                                <?php if(!empty($cov['photo_chauffeur'])): ?>
                                    <img src="<?= PUBLIC_URL ?>/uploads/<?= htmlspecialchars((string)$cov['photo_chauffeur']) ?>" 
                                         alt="Photo chauffeur" class="img-thumbnail" style="max-width:80px;">
                                <?php else: ?>
                                    <img src="<?= PUBLIC_URL ?>/images/man.png" 
                                         alt="Logo chauffeur" class="img-thumbnail" style="max-width:80px;">
                                <?php endif; ?>
                            </p>
                            <p class="mb-1">Note : <?= htmlspecialchars((string)($cov['note_moyenne'] ?? '0')) ?> ⭐</p>
                            <?php if(!empty($cov['electrique'])): ?>
                                <p class="mb-1"><img src="<?= PUBLIC_URL ?>/assets/logo_electrique.png" alt="Électrique" style="width:24px;"></p>
                            <?php endif; ?>

                            <!-- Bouton voir détails -->
                            <button class="btn btn-info btn-sm mt-2" type="button" 
                                    onclick="toggleDetails('details-<?= $cov_id ?>')">
                                Voir détails
                            </button>

                            <!-- Section détails cachée -->
                            <div id="details-<?= $cov_id ?>" class="mt-2" style="display:none; border-top:1px solid #ddd; padding-top:10px;">
                                <p>Marque véhicule : <?= htmlspecialchars((string)($cov['marque'] ?? '-')) ?></p>
                                <p>Modèle véhicule : <?= htmlspecialchars((string)($cov['modele'] ?? '-')) ?></p>
                                <?php 
                                    $prefs = $cov['preferences'] ?? ['fumeur'=>0,'animal'=>0,'remarques_particulieres'=>'-'];
                                ?>
                                <p>Préférences chauffeur :</p>
                                <ul>
                                    <li>Fumeur : <?= !empty($prefs['fumeur']) ? 'Oui' : 'Non' ?></li>
                                    <li>Animaux : <?= !empty($prefs['animal']) ? 'Oui' : 'Non' ?></li>
                                    <li>Remarques : <?= htmlspecialchars((string)($prefs['remarques_particulieres'] ?? '-')) ?></li>
                                </ul>
                            </div>

                            <div class="mt-2 text-end">
                                <a href="?page=participer_covoiturage&id=<?= $cov_id ?>" class="btn btn-success btn-sm">Participer</a>
                            </div>
                        </div>
                    </div>  
                </div>
            <?php endforeach; ?>
        </div>
 
        <!-- Pagination -->
        <?php if(!empty($total_pages) && !empty($page_num)): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-3">
                <?php for($i=1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page_num) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= PUBLIC_URL ?>/?page=recherche_covoiturage&ville_depart=<?= urlencode((string)$ville_depart) ?>&ville_arrivee=<?= urlencode((string)$ville_arrivee) ?>&date_depart=<?= urlencode((string)$date_depart) ?>&page_num=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-center">Aucun covoiturage trouvé.</p>
    <?php endif; ?>
</div>

<script>
function toggleDetails(id) {
    const el = document.getElementById(id);
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
}
</script>

<?php include APP_PATH . '/vues/pied_de_page.php'; ?>
