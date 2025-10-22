<?php
require_once ROOT_PATH . '/config/config.php'; // connexion à la BDD

// récupération de la liste des trajets disponibles

function rechercherCovoiturages($ville_depart, $ville_arrivee, $date_depart, $filtres = [], $limit = 5, $offset = 0) {
    global $pdo;

    $sql = "SELECT c.*, v.marque, v.modele, v.id_type_motorisation,
                   u.nom AS nom_chauffeur,
                   u.photo AS photo_chauffeur,
                   (SELECT AVG((valeur1+valeur2+valeur3+valeur4+valeur5)/5) 
                    FROM note n WHERE n.id_covoiturage = c.id_covoiturage) AS note_moyenne,
                   (v.id_type_motorisation = 4) AS electrique,
                   TIMESTAMPDIFF(HOUR, CONCAT(c.date_depart,' ',c.heure_depart), CONCAT(c.date_arrivee,' ',c.heure_arrivee)) AS duree
            FROM covoiturage c
            JOIN vehicule v ON c.id_vehicule = v.id_vehicule
            JOIN compte u ON c.id_utilisateur = u.id_utilisateur
            WHERE c.lieu_depart LIKE :ville_depart
              AND c.lieu_arrivee LIKE :ville_arrivee
              AND c.date_depart = :date_depart"; 

    $params = [
        ':ville_depart'  => "%$ville_depart%",
        ':ville_arrivee' => "%$ville_arrivee%",
        ':date_depart'   => $date_depart
    ];

    if (!empty($filtres['electrique'])) {
        $sql .= " AND v.id_type_motorisation = 4";
    }
    if (!empty($filtres['prix_max'])) {
        $sql .= " AND c.prix_par_personne <= :prix_max";
        $params[':prix_max'] = $filtres['prix_max'];
    }
    if (!empty($filtres['duree_max'])) {
        $sql .= " AND TIMESTAMPDIFF(HOUR, CONCAT(c.date_depart,' ',c.heure_depart), CONCAT(c.date_arrivee,' ',c.heure_arrivee)) <= :duree_max";
        $params[':duree_max'] = $filtres['duree_max'];
    }

    $sql .= " ORDER BY c.date_depart ASC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $covoiturages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les préférences pour chaque covoiturage
    foreach ($covoiturages as &$cov) {
        $stmt2 = $pdo->prepare("SELECT fumeur, animal, remarques_particulieres 
                                FROM preference 
                                WHERE id_utilisateur = :id_utilisateur");
        $stmt2->execute([':id_utilisateur' => $cov['id_utilisateur']]);
        $prefs = $stmt2->fetch(PDO::FETCH_ASSOC);
        $cov['preferences'] = $prefs ?: null;
    }

    return $covoiturages;
}

function compterCovoiturages($ville_depart, $ville_arrivee, $date_depart, $filtres = []) {
    global $pdo;

    $sql = "SELECT COUNT(*) 
            FROM covoiturage c
            JOIN vehicule v ON c.id_vehicule = v.id_vehicule
            WHERE c.lieu_depart LIKE :ville_depart
              AND c.lieu_arrivee LIKE :ville_arrivee
              AND c.date_depart = :date_depart";

    $params = [
        ':ville_depart'  => "%$ville_depart%",
        ':ville_arrivee' => "%$ville_arrivee%",
        ':date_depart'   => $date_depart
    ];

    if (!empty($filtres['electrique'])) {
        $sql .= " AND v.id_type_motorisation = 4";
    }
    if (!empty($filtres['prix_max'])) {
        $sql .= " AND c.prix_par_personne <= :prix_max";
        $params[':prix_max'] = $filtres['prix_max'];
    }
    if (!empty($filtres['duree_max'])) {
        $sql .= " AND TIMESTAMPDIFF(HOUR, CONCAT(c.date_depart,' ',c.heure_depart), CONCAT(c.date_arrivee,' ',c.heure_arrivee)) <= :duree_max";
        $params[':duree_max'] = $filtres['duree_max'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}
