<?php
require_once ROOT_PATH . '/config/config.php';
require_once APP_PATH . '/modeles/reservation.php';

/**
 * Créer un covoiturage
 */
function creerVoyage(int $id_utilisateur, array $data): string {
    global $pdo;

    if (empty($data['ville_depart']) || empty($data['ville_arrivee']) ||
        empty($data['date_depart']) || empty($data['heure_depart']) ||
        empty($data['date_arrivee']) || empty($data['heure_arrivee']) ||
        empty($data['prix']) || empty($data['id_vehicule'])) {
        return "Tous les champs sont obligatoires.";
    }

    $sql = "INSERT INTO covoiturage 
            (id_utilisateur, id_vehicule, lieu_depart, lieu_arrivee, date_depart, heure_depart, date_arrivee, heure_arrivee, prix_par_personne)
            VALUES
            (:id_utilisateur, :id_vehicule, :lieu_depart, :lieu_arrivee, :date_depart, :heure_depart, :date_arrivee, :heure_arrivee, :prix)";

    $stmt = $pdo->prepare($sql);

    $params = [
        'id_utilisateur' => $id_utilisateur,
        'id_vehicule'    => $data['id_vehicule'],
        'lieu_depart'    => $data['ville_depart'],
        'lieu_arrivee'   => $data['ville_arrivee'],
        'date_depart'    => $data['date_depart'],
        'heure_depart'   => $data['heure_depart'],
        'date_arrivee'   => $data['date_arrivee'],
        'heure_arrivee'  => $data['heure_arrivee'],
        'prix'           => $data['prix']
    ];

    if ($stmt->execute($params)) {
        return "Covoiturage créé avec succès.";
    } else {
        return "Erreur lors de la création du covoiturage.";
    }
}

/**
 * Récupérer les covoiturages programmés d'un utilisateur avec leur statut
 */
function getCovoituragesProgrammesByUtilisateur(int $id_utilisateur): array {
    global $pdo;

    $sql = "
        SELECT c.*, 
               s.statut, 
               v.marque, 
               v.modele, 
               v.id_utilisateur AS vehicule_proprietaire,
               u.nom AS nom_chauffeur
        FROM covoiturage c
        LEFT JOIN statut_covoiturage s ON c.id_covoiturage = s.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE c.id_utilisateur = :id_utilisateur
        ORDER BY c.date_depart, c.heure_depart
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_utilisateur' => $id_utilisateur]);
    $covoiturages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si aucun statut n'existe, mettre "prévu" par défaut
    foreach ($covoiturages as &$c) {
        if (empty($c['statut'])) {
            $c['statut'] = 'prévu';
        }
    }

    return $covoiturages;
}

/**
 * Changer le statut d'un covoiturage
 */
function changerStatutCovoiturage(int $id_covoiturage, string $statut): bool {
    global $pdo;

    // Vérifie si le statut existe déjà
    $stmt = $pdo->prepare("SELECT id_statut FROM statut_covoiturage WHERE id_covoiturage=:id");
    $stmt->execute(['id' => $id_covoiturage]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE statut_covoiturage SET statut=:statut WHERE id_covoiturage=:id");
    } else {
        $stmt = $pdo->prepare("INSERT INTO statut_covoiturage (statut, id_covoiturage) VALUES (:statut, :id)");
    }

    return $stmt->execute(['statut' => $statut, 'id' => $id_covoiturage]);
}


/**
 * Récupérer les participants d'un covoiturage
 */
function getParticipants(int $id_covoiturage): array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT u.nom, u.mail 
        FROM reservation r
        JOIN compte u ON r.id_utilisateur = u.id_utilisateur
        WHERE r.id_covoiturage = :id_covoiturage
    ");
    $stmt->execute(['id_covoiturage' => $id_covoiturage]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupérer un covoiturage par son ID
 */
function getCovoiturageById(int $id_covoiturage): ?array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT c.*, 
               v.marque, 
               v.modele, 
               u.nom AS nom_chauffeur
        FROM covoiturage c
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE c.id_covoiturage = :id_covoiturage
        LIMIT 1
    ");
    $stmt->execute(['id_covoiturage' => $id_covoiturage]);
    $cov = $stmt->fetch(PDO::FETCH_ASSOC);

    return $cov ?: null;
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

function rechercherCovoiturages(array $criteres, int $limit, int $offset): array {
    global $pdo;

    $sql = "SELECT * FROM covoiturage WHERE 1=1";
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

    if (!empty($criteres['electrique'])) {
        $sql .= " AND vehicule_electrique = 1";
    }
    if (!empty($criteres['prix_max'])) {
        $sql .= " AND prix_par_personne <= :prix_max";
        $params['prix_max'] = $criteres['prix_max'];
    }

    $sql .= " ORDER BY date_depart, heure_depart LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
