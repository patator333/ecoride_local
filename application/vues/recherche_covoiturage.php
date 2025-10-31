<?php include APP_PATH . '/vues/entete.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Recherche de covoiturage</h2>

    <!-- Formulaire de recherche -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="page" value="recherche_covoiturage">

        <div class="col-12 col-md-4">
            <input type="text" name="ville_depart" class="form-control" placeholder="Ville de départ"
                   value="<?= htmlspecialchars((string)($ville_depart ?? '')) ?>" required>
        </div>
        <div class="col-12 col-md-4">
            <input type="text" name="ville_arrivee" class="form-control" placeholder="Ville d'arrivée"
                   value="<?= htmlspecialchars((string)($ville_arrivee ?? '')) ?>" required>
        </div>
        <div class="col-12 col-md-3">
            <input type="date" name="date_depart" class="form-control"
                   value="<?= htmlspecialchars((string)($date_depart ?? '')) ?>" required>
        </div>
        <div class="col-12 col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Valider</button>
        </div>
    </form>

    <!-- 🔹 Filtres supplémentaires -->
    <?php if (!empty($covoiturages)): ?>
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="page" value="recherche_covoiturage">
        <input type="hidden" name="ville_depart" value="<?= htmlspecialchars((string)$ville_depart) ?>">
        <input type="hidden" name="ville_arrivee" value="<?= htmlspecialchars((string)$ville_arrivee) ?>">
        <input type="hidden" name="date_depart" value="<?= htmlspecialchars((string)$date_depart) ?>">

        <div class="col-12 col-md-3">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="electrique" value="1"
                       <?= !empty($_GET['electrique']) ? 'checked' : '' ?>>
                <label class="form-check-label">Voiture électrique uniquement</label>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <input type="number" name="prix_max" min="0" step="1" class="form-control"
                   placeholder="Prix max (€)"
                   value="<?= htmlspecialchars((string)($_GET['prix_max'] ?? '')) ?>">
        </div>

        <div class="col-12 col-md-3">
            <input type="number" name="duree_max" min="0" step="1" class="form-control"
                   placeholder="Durée max (h)"
                   value="<?= htmlspecialchars((string)($_GET['duree_max'] ?? '')) ?>">
        </div>

        <div class="col-12 col-md-2">
            <input type="number" name="note_min" min="0" max="5" step="0.1" class="form-control"
                   placeholder="Note min"
                   value="<?= htmlspecialchars((string)($_GET['note_min'] ?? '')) ?>">
        </div>

        <div class="col-12 col-md-1 d-grid">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
        </div>
    </form>
    <?php endif; ?>

    <!-- Résultats -->
    <?php if (!empty($covoiturages) && is_array($covoiturages)): ?>
        <div class="row">
            <?php foreach ($covoiturages as $cov): ?>
                <?php $cov_id = $cov['id_covoiturage'] ?? 0; ?>
                <div class="col-12 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= htmlspecialchars((string)($cov['lieu_depart'] ?? '-') . " → " . ($cov['lieu_arrivee'] ?? '-')) ?>
                            </h5>
                            <p class="mb-1">Date : <?= htmlspecialchars((string)($cov['date_depart'] ?? '-')) ?></p>
                            <p class="mb-1">Heure : <?= htmlspecialchars((string)($cov['heure_depart'] ?? '-')) ?></p>
                            <p class="mb-1">Crédit demandé : <?= htmlspecialchars((string)($cov['prix_par_personne'] ?? '-')) ?> €</p>
                            <p class="mb-1">Durée estimée : <?= htmlspecialchars((string)($cov['duree'] ?? '-')) ?> h</p>
                            <p class="mb-1">Places restantes : <?= htmlspecialchars((string)($cov['nombre_places'] ?? '-')) ?></p>
                            <p class="mb-1">Chauffeur : <?= htmlspecialchars((string)($cov['nom_chauffeur'] ?? '-')) ?></p>
                            <p class="mb-1 text-center">
                                <?php if (!empty($cov['photo_chauffeur'])): ?>
                                    <img src="<?= PUBLIC_URL ?>/uploads/<?= htmlspecialchars((string)$cov['photo_chauffeur']) ?>"
                                         alt="Photo chauffeur" class="img-thumbnail" style="max-width:80px;">
                                <?php else: ?>
                                    <img src="<?= PUBLIC_URL ?>/images/man.png"
                                         alt="Logo chauffeur" class="img-thumbnail" style="max-width:80px;">
                                <?php endif; ?>
                            </p>
                            <p class="mb-1">Note : <?= htmlspecialchars((string)($cov['note_moyenne'] ?? '0')) ?> ⭐</p>
                            <?php if (!empty($cov['vehicule_electrique'])): ?>
                                <p class="mb-1 text-center">
                                    <img src="<?= PUBLIC_URL ?>/assets/logo_electrique.png" alt="Électrique" style="width:24px;">
                                </p>
                            <?php endif; ?>

                            <div class="d-grid gap-2 mt-2">
                                <button class="btn btn-info btn-sm" type="button"
                                        onclick="toggleDetails('details-<?= $cov_id ?>')">
                                    Voir détails
                                </button>

                                <div id="details-<?= $cov_id ?>" class="mt-2 border-top pt-2" style="display:none;">
                                    <p>Marque véhicule : <?= htmlspecialchars((string)($cov['marque'] ?? '-')) ?></p>
                                    <p>Modèle véhicule : <?= htmlspecialchars((string)($cov['modele'] ?? '-')) ?></p>
                                    <?php 
                                        $prefs = $cov['preferences'] ?? ['fumeur'=>0,'animal'=>0,'remarques_particulieres'=>'-'];
                                    ?>
                                    <p>Préférences chauffeur :</p>
                                    <ul class="mb-0">
                                        <li>Fumeur : <?= !empty($prefs['fumeur']) ? 'Oui' : 'Non' ?></li>
                                        <li>Animaux : <?= !empty($prefs['animal']) ? 'Oui' : 'Non' ?></li>
                                        <li>Remarques : <?= htmlspecialchars((string)($prefs['remarques_particulieres'] ?? '-')) ?></li>
                                    </ul>
                                </div>

                                <a href="?page=participer_covoiturage&id=<?= $cov_id ?>" class="btn btn-success btn-sm mt-2">
                                    Participer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($total_pages) && !empty($page_num)): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3 flex-wrap">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page_num) ? 'active' : '' ?>">
                            <a class="page-link"
                               href="<?= PUBLIC_URL ?>/?page=recherche_covoiturage&ville_depart=<?= urlencode((string)$ville_depart) ?>&ville_arrivee=<?= urlencode((string)$ville_arrivee) ?>&date_depart=<?= urlencode((string)$date_depart) ?>&page_num=<?= $i ?>">
                                <?= $i ?>
                            </a>
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
