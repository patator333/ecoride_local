<?php
require_once ROOT_PATH . '/config/config.php'; // $pdo doit être défini

/**
 * Récupérer un covoiturage par son ID
 */
function getCovoiturageById(int $id_covoiturage): ?array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT c.*, 
               v.marque, v.modele,
               u.nom AS nom_chauffeur
        FROM covoiturage c
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE c.id_covoiturage = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id_covoiturage]);
    $cov = $stmt->fetch(PDO::FETCH_ASSOC);

    return $cov ?: null;
}

/**
 * Récupérer tous les participants d'un covoiturage
 */
function getParticipants(int $id_covoiturage): array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT u.nom, u.mail
        FROM reservation r
        JOIN compte u ON r.id_utilisateur = u.id_utilisateur
        WHERE r.id_covoiturage = :id
    ");
    $stmt->execute([':id' => $id_covoiturage]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Compter les covoiturages correspondant aux critères
 */
function compterCovoiturages(array $criteres): int {
    global $pdo;

    $sql = "SELECT COUNT(*) FROM covoiturage WHERE 1=1";
    $params = [];

    if (!empty($criteres['ville_depart'])) {
        $sql .= " AND lieu_depart = :ville_depart";
        $params['ville_depart'] = $criteres['ville_depart'];
    }
    if (!empty($criteres['ville_arrivee'])) {
        $sql .= " AND lieu_arrivee = :ville_arrivee";
        $params['ville_arrivee'] = $criteres['ville_arrivee'];
    }
    if (!empty($criteres['date_depart'])) {
        $sql .= " AND date_depart = :date_depart";
        $params['date_depart'] = $criteres['date_depart'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

/**
 * Rechercher des covoiturages avec critères, pagination
 */
function rechercherCovoiturages(array $criteres, int $limit, int $offset): array {
    global $pdo;

    $sql = "SELECT c.*, v.marque, v.modele
            FROM covoiturage c
            LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
            WHERE 1=1";
    $params = [];

    if (!empty($criteres['ville_depart'])) {
        $sql .= " AND c.lieu_depart = :ville_depart";
        $params['ville_depart'] = $criteres['ville_depart'];
    }
    if (!empty($criteres['ville_arrivee'])) {
        $sql .= " AND c.lieu_arrivee = :ville_arrivee";
        $params['ville_arrivee'] = $criteres['ville_arrivee'];
    }
    if (!empty($criteres['date_depart'])) {
        $sql .= " AND c.date_depart = :date_depart";
        $params['date_depart'] = $criteres['date_depart'];
    }
    if (!empty($criteres['electrique'])) {
        $sql .= " AND v.vehicule_electrique = 1";
    }
    if (!empty($criteres['prix_max'])) {
        $sql .= " AND c.prix_par_personne <= :prix_max";
        $params['prix_max'] = $criteres['prix_max'];
    }

    $sql .= " ORDER BY c.date_depart, c.heure_depart LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Changer le statut d'un covoiturage
 */
function changerStatutCovoiturage(int $id_covoiturage, string $statut): bool {
    global $pdo;

    // Vérifie si le statut existe déjà
    $stmt = $pdo->prepare("SELECT id_statut FROM statut_covoiturage WHERE id_covoiturage = :id");
    $stmt->execute([':id' => $id_covoiturage]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE statut_covoiturage SET statut = :statut WHERE id_covoiturage = :id");
    } else {
        $stmt = $pdo->prepare("INSERT INTO statut_covoiturage (statut, id_covoiturage) VALUES (:statut, :id)");
    }

    return $stmt->execute([':statut' => $statut, ':id' => $id_covoiturage]);
}


/**
 * Récupérer les covoiturages programmés pour un utilisateur
 * (ceux qu'il a créés ou auxquels il participe)
 */
/*
function getCovoituragesPourUtilisateur(int $id_utilisateur): array {
    global $pdo;

    // Covoiturages créés par l'utilisateur
    $stmt1 = $pdo->prepare("
        SELECT c.*, sc.statut, v.marque, v.modele, u.nom AS nom_chauffeur
        FROM covoiturage c
        LEFT JOIN statut_covoiturage sc ON c.id_covoiturage = sc.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE c.id_utilisateur = :id
    ");
    $stmt1->execute([':id' => $id_utilisateur]);
    $crees = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Covoiturages où l'utilisateur est participant
    $stmt2 = $pdo->prepare("
        SELECT c.*, sc.statut, v.marque, v.modele, u.nom AS nom_chauffeur
        FROM reservation r
        JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
        LEFT JOIN statut_covoiturage sc ON c.id_covoiturage = sc.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE r.id_utilisateur = :id
        AND c.id_utilisateur != :id
    ");
    $stmt2->execute([':id' => $id_utilisateur]);
    $participe = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Fusionner les deux listes
    return array_merge($crees, $participe);
}*/
