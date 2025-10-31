<?php include APP_PATH . '/vues/entete.php'; ?>
<div class="container mt-4">

    <!-- Déconnexion -->
    <div class="d-flex justify-content-end mb-3">
        <a href="index.php?page=deconnexion" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>

    <!-- ROLE UTILISATEUR -->
    <h3 class="text-center">Votre rôle</h3>
    <?php if(!empty($message)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST" class="text-center mb-4">
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <label><input type="radio" name="role" value="1" <?= ($user['id_role']==1)?'checked':'' ?>> Passager</label>
            <label><input type="radio" name="role" value="2" <?= ($user['id_role']==2)?'checked':'' ?>> Chauffeur</label>
            <label><input type="radio" name="role" value="3" <?= ($user['id_role']==3)?'checked':'' ?>> Chauffeur & Passager</label>
        </div>
        <button type="submit" class="btn btn-success btn-sm mt-2">Valider</button>
    </form>

    <!-- VEHICULES -->
    <?php if($user['id_role'] != 1): ?>
        <h3 class="text-center">Mes véhicules</h3>
        <?php if(!empty($vehicule_message)): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($vehicule_message) ?></div>
        <?php endif; ?>

        <?php if(count($vehicules)==0): ?>
            <p class="text-center">Aucun véhicule enregistré.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Immatriculation</th>
                            <th>Date 1ère immat.</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Places</th>
                            <th>Motorisation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vehicules as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['immatriculation'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['date_de_premiere_immatriculation'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['marque'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['modele'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['places_disponibles'] ?? '') ?></td>
                                <td><?= htmlspecialchars($v['motorisation'] ?? $v['id_type_motorisation']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <h4 class="text-center mt-4">Ajouter un véhicule</h4>
        <form method="POST" class="mb-4">
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="text" name="immatriculation" class="form-control" placeholder="Immatriculation" required></div>
                <div class="col-12 col-md"><input type="date" name="date_de_premiere_immatriculation" class="form-control" required></div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="text" name="marque" class="form-control" placeholder="Marque" required></div>
                <div class="col-12 col-md"><input type="text" name="modele" class="form-control" placeholder="Modèle" required></div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="text" name="couleur" class="form-control" placeholder="Couleur"></div>
                <div class="col-12 col-md"><input type="number" name="places_disponibles" class="form-control" placeholder="Places" required></div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12 col-md">
                    <select name="id_type_motorisation" class="form-control" required>
                        <option value="1">Essence</option>
                        <option value="2">Diesel</option>
                        <option value="3">Hybride</option>
                        <option value="4">Electrique</option>
                    </select>
                </div>
                <div class="col-12 col-md">
                    <button type="submit" name="ajouter_vehicule" class="btn btn-primary w-100">Ajouter ce véhicule</button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <!-- PREFERENCES -->
    <h3 class="text-center mt-4">Préférences</h3>
    <?php if(!empty($pref_message)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($pref_message) ?></div>
    <?php endif; ?>
    <form method="POST" class="mb-4 text-center">
        <div class="mb-2 d-flex justify-content-center flex-wrap gap-3">
            <label><input type="checkbox" name="fumeur" <?= (!empty($preferences['fumeur']))?'checked':'' ?>> Fumeur</label>
            <label><input type="checkbox" name="animal" <?= (!empty($preferences['animal']))?'checked':'' ?>> Animal</label>
        </div>
        <input type="text" name="remarques_particulieres" class="form-control my-2" placeholder="Remarques particulières" value="<?= htmlspecialchars($preferences['remarques_particulieres'] ?? '') ?>">
        <button type="submit" name="valider_preferences" class="btn btn-success btn-sm">Valider</button>
    </form>

    <!-- CREER VOYAGE -->
    <?php if($user['id_role'] != 1): ?>
        <h3 class="text-center mt-4">Créer un voyage</h3>
        <?php if(!empty($voyage_message)): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($voyage_message) ?></div>
        <?php endif; ?>
        <form method="POST" class="mb-4">
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="text" name="ville_depart" class="form-control" placeholder="Ville de départ" required></div>
                <div class="col-12 col-md"><input type="text" name="ville_arrivee" class="form-control" placeholder="Ville d'arrivée" required></div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="date" name="date_depart" class="form-control" required></div>
                <div class="col-12 col-md"><input type="time" name="heure_depart" class="form-control" required></div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="date" name="date_arrivee" class="form-control" required></div>
                <div class="col-12 col-md"><input type="time" name="heure_arrivee" class="form-control" required></div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-12 col-md"><input type="number" name="prix" class="form-control" placeholder="Prix (2 crédits inclus)" required></div>
                <div class="col-12 col-md">
                    <select name="id_vehicule" class="form-control" required>
                        <?php foreach($vehicules as $v): ?>
                            <option value="<?= $v['id_vehicule'] ?>"><?= htmlspecialchars($v['marque'].' '.$v['modele'].' ('.$v['immatriculation'].')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="creer_voyage" class="btn btn-primary w-100">Valider le voyage</button>
        </form>
    <?php endif; ?>

    <!-- HISTORIQUE -->
    <h3 class="text-center mt-4">Historique des covoiturages</h3>
    <?php if(empty($historique)): ?>
        <p class="text-center">Aucun covoiturage réalisé.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Date</th>
                        <th>Heure départ</th>
                        <th>Heure arrivée</th>
                        <th>Prix</th>
                        <th>Chauffeur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($historique as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['lieu_depart'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['lieu_arrivee'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['date_depart'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['heure_depart'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['heure_arrivee'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['prix_par_personne'] ?? '') ?></td>
                            <td><?= htmlspecialchars($h['nom_chauffeur'] ?? '') ?></td>
                            <td>
                                <?php if(!empty($h['peut_ajouter_avis'])): ?>
                                    <a href="index.php?page=avis&id_covoiturage=<?= (int)$h['id_covoiturage'] ?>" class="btn btn-sm btn-primary">Donner un avis</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- COVOITURAGES PROGRAMMES -->
    <h3 class="text-center mt-4">Covoiturages programmés</h3>
    <?php if(empty($covoiturages_programmes)): ?>
        <p class="text-center">Aucun covoiturage programmé.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Date départ</th>
                        <th>Heure départ</th>
                        <th>Chauffeur</th>
                        <th>Véhicule</th>
                        <th>Actions / Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($covoiturages_programmes as $c):
                        $statut = $c['statut'] ?? 'prévu';
                    ?>
                        <tr class="<?= $statut=='annulé' ? 'table-danger' : ($statut=='en_cours' ? 'table-warning' : '') ?>">
                            <td><?= htmlspecialchars($c['id_covoiturage'] ?? '') ?></td>
                            <td><?= htmlspecialchars($c['lieu_depart'] ?? '') ?></td>
                            <td><?= htmlspecialchars($c['lieu_arrivee'] ?? '') ?></td>
                            <td><?= htmlspecialchars($c['date_depart'] ?? '') ?></td>
                            <td><?= htmlspecialchars($c['heure_depart'] ?? '') ?></td>
                            <td><?= htmlspecialchars($c['nom_chauffeur'] ?? '') ?></td>
                            <td><?= htmlspecialchars(($c['marque'] ?? '').' '.($c['modele'] ?? '')) ?></td>
                            <td class="d-flex flex-wrap justify-content-center gap-1">
                                <?php if(($c['id_utilisateur'] ?? 0) == $_SESSION['user']['id_utilisateur']): ?>
                                    <?php if($statut=='prévu'): ?>
                                        <a href="index.php?page=action_covoiturage&id=<?= $c['id_covoiturage'] ?>&action=demarrer" class="btn btn-success btn-sm">Démarrer</a>
                                    <?php elseif($statut=='en_cours'): ?>
                                        <a href="index.php?page=action_covoiturage&id=<?= $c['id_covoiturage'] ?>&action=terminer" class="btn btn-warning btn-sm">Terminer</a>
                                    <?php endif; ?>
                                    <a href="index.php?page=action_covoiturage&id=<?= $c['id_covoiturage'] ?>&action=annuler" class="btn btn-danger btn-sm">Annuler</a>
                                <?php endif; ?>
                                <span class="badge bg-info text-dark"><?= ucfirst($statut) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
