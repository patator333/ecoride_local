<?php
require_once ROOT_PATH . '/config/config.php';
require_once APP_PATH . '/modeles/reservation.php';

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
